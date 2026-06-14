<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActualizarFechaTomaMuestraRequest;
use App\Http\Requests\StoreServicioRequest;
use App\Http\Requests\UpdateServicioRequest;
use App\Models\Examen;
use App\Models\LaboratorioExamen;
use App\Models\Profesional;
use App\Models\Servicio;
use App\Models\ServicioExamen;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ServicioController extends Controller
{
    public function index(Request $request)
    {
        $query = Servicio::with(['cliente', 'serviciosExamen']);

        // Filtros
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        if ($request->filled('documento')) {
            $documento = $request->documento;
            $query->whereHas('cliente', function ($q) use ($documento) {
                $q->where('documento', 'like', "%{$documento}%");
            });
        }

        if ($request->filled('buscar')) {
            $termino = $request->buscar;
            $query->whereHas('cliente', function ($q) use ($termino) {
                $q->where('nombre', 'like', "%{$termino}%")
                    ->orWhere('apellido', 'like', "%{$termino}%");
            });
        }

        if ($request->filled('examenes')) {
            $examenesIds = $request->examenes;
            $query->whereHas('serviciosExamen', function ($q) use ($examenesIds) {
                $q->whereIn('examen_id', $examenesIds);
            });
        }

        if ($request->filled('estado_examen')) {
            $estadoExamen = $request->estado_examen;
            $query->whereHas('serviciosExamen', function ($q) use ($estadoExamen) {
                $q->where('estado', $estadoExamen);
            });
        }

        if ($request->ajax()) {
            $servicios = $query->latest('fecha')->get();

            return response()->json([
                'data' => $servicios->map(function ($servicio) {
                    $estadoPagoBadge = match ($servicio->estado_pago) {
                        'PENDIENTE' => '<span class="badge bg-warning text-dark">Pendiente</span>',
                        'PARCIAL' => '<span class="badge bg-info">Parcial</span>',
                        default => '<span class="badge bg-success">Pagado</span>',
                    };

                    return [
                        'numero_orden' => $servicio->numero_orden,
                        'fecha' => $servicio->fecha->format('d/m/Y'),
                        'cliente_nombre' => $servicio->cliente->nombre_completo,
                        'documento' => $servicio->cliente->documento,
                        'total_examenes' => $servicio->serviciosExamen->count(),
                        'valor_total' => '$'.number_format($servicio->valor_total, 0, ',', '.'),
                        'estado_pago' => $estadoPagoBadge,
                        'acciones' => $servicio->id,
                    ];
                }),
            ]);
        }

        $examenesDisponibles = Examen::activos()->orderBy('nombre')->get();
        $estadosExamen = [
            'PENDIENTE' => 'Pendiente',
            'EN_PROCESO' => 'En Proceso',
            'COMPLETADO' => 'Completado',
            'VALIDADO' => 'Validado',
            'ENTREGADO' => 'Entregado',
        ];

        return view('servicios.index', compact('examenesDisponibles', 'estadosExamen'));
    }

    public function create()
    {
        $examenes = Examen::where('status', 1)
            ->with('categoria')
            ->orderBy('categoria_id')
            ->orderBy('nombre')
            ->get()
            ->groupBy('categoria.categoria');

        $profesionales = Profesional::where('status', 1)
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->get();

        return view('servicios.create', compact('examenes', 'profesionales'));
    }

    public function store(StoreServicioRequest $request)
    {
        try {
            DB::beginTransaction();

            // Calcular valor total
            $valorTotal = array_sum($request->precios);

            // Calcular estado de pago
            $valorPagado = $request->valor_pagado ?? 0;
            $estadoPago = $this->calcularEstadoPago($valorTotal, $valorPagado);

            // Generar nÃºmero de orden
            $numeroOrden = $this->generarNumeroOrden();

            // Crear servicio
            $servicio = Servicio::create([
                'cliente_id' => $request->cliente_id,
                'numero_orden' => $numeroOrden,
                'fecha' => $request->fecha,
                'valor_total' => $valorTotal,
                'valor_pagado' => $valorPagado,
                'medio_pago' => $request->medio_pago,
                'canal_difusion' => $request->canal_difusion,
                'estado_pago' => $estadoPago,
                'observaciones' => $request->observaciones,
            ]);

            // Crear servicios_examen
            foreach ($request->examenes as $index => $examenId) {
                ServicioExamen::create([
                    'servicio_id' => $servicio->id,
                    'examen_id' => $examenId,
                    'es_remitido' => (bool) ($request->remitidos[$index] ?? false),
                    'estado' => 'PENDIENTE',
                ]);
            }

            DB::commit();

            return redirect()->route('servicios.show', $servicio)
                ->with('success', 'Servicio creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Error al crear el servicio: '.$e->getMessage());
        }
    }

    public function show(Servicio $servicio)
    {
        $servicio->load([
            'cliente.ips',
            'serviciosExamen.examen.categoria',
            'serviciosExamen.profesional',
            'serviciosExamen.resultados',
        ]);

        $profesionales = Profesional::where('status', 1)
            // ->where('profesion', 'BacteriÃ³logo')
            ->orderBy('nombre')
            ->get();

        return view('servicios.show', compact('servicio', 'profesionales'));
    }

    public function edit(Servicio $servicio)
    {
        // Verificar que no tenga exÃ¡menes con resultados
        $tieneResultados = $servicio->serviciosExamen()
            ->whereIn('estado', ['COMPLETADO', 'VALIDADO', 'ENTREGADO'])
            ->exists();

        if ($tieneResultados) {
            return back()->with('error', 'No se puede editar un servicio con exÃ¡menes que ya tienen resultados.');
        }

        $servicio->load(['cliente', 'serviciosExamen.examen']);

        $examenes = Examen::where('status', 1)
            ->with('categoria')
            ->orderBy('categoria_id')
            ->orderBy('nombre')
            ->get()
            ->groupBy('categoria.categoria');

        return view('servicios.edit', compact('servicio', 'examenes'));
    }

    public function update(UpdateServicioRequest $request, Servicio $servicio)
    {
        try {
            DB::beginTransaction();

            // Si se estÃ¡n actualizando exÃ¡menes, verificar que no tengan resultados
            if ($request->has('examenes')) {
                $tieneResultados = $servicio->serviciosExamen()
                    ->whereIn('estado', ['COMPLETADO', 'VALIDADO', 'ENTREGADO'])
                    ->exists();

                if ($tieneResultados) {
                    return back()->with('error', 'No se pueden modificar exÃ¡menes que ya tienen resultados.');
                }

                // Eliminar exÃ¡menes actuales solo si estÃ¡n pendientes
                $servicio->serviciosExamen()->where('estado', 'PENDIENTE')->delete();

                // Agregar nuevos exÃ¡menes
                foreach ($request->examenes as $index => $examenId) {
                    ServicioExamen::create([
                        'servicio_id' => $servicio->id,
                        'examen_id' => $examenId,
                        'estado' => 'PENDIENTE',
                    ]);
                }

                // Recalcular valor total
                $valorTotal = array_sum($request->precios);
                $servicio->valor_total = $valorTotal;
            }

            // Actualizar datos del servicio
            if ($request->filled('cliente_id')) {
                $servicio->cliente_id = $request->cliente_id;
            }

            if ($request->filled('fecha')) {
                $servicio->fecha = $request->fecha;
            }

            if ($request->filled('valor_pagado')) {
                $servicio->valor_pagado = $request->valor_pagado;
                $servicio->estado_pago = $this->calcularEstadoPago($servicio->valor_total, $request->valor_pagado);
            }

            if ($request->filled('medio_pago')) {
                $servicio->medio_pago = $request->medio_pago;
            }

            if ($request->has('canal_difusion')) {
                $servicio->canal_difusion = $request->canal_difusion ?: null;
            }

            if ($request->has('observaciones')) {
                $servicio->observaciones = $request->observaciones;
            }

            $servicio->save();

            DB::commit();

            return redirect()->route('servicios.show', $servicio)
                ->with('success', 'Servicio actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Error al actualizar el servicio: '.$e->getMessage());
        }
    }

    public function descargarOrden(Servicio $servicio)
    {
        $servicio->load([
            'cliente',
            'serviciosExamen.examen.categoria',
        ]);

        $empresa = \App\Models\Empresa::first();

        $pdf = Pdf::loadView('servicios.orden-pdf', compact('servicio', 'empresa'));
        $pdf->setPaper('letter', 'portrait');

        return $pdf->download("orden-{$servicio->numero_orden}.pdf");
    }

    public function destroy(Servicio $servicio)
    {
        try {
            DB::beginTransaction();

            foreach ($servicio->serviciosExamen()->with('adjuntos')->get() as $servicioExamen) {
                if ($servicioExamen->pdf_remision && Storage::disk('public')->exists($servicioExamen->pdf_remision)) {
                    Storage::disk('public')->delete($servicioExamen->pdf_remision);
                }

                // Eliminar adjuntos uno a uno para disparar el evento deleting() que borra el archivo físico
                foreach ($servicioExamen->adjuntos as $adjunto) {
                    $adjunto->delete();
                }

                $servicioExamen->resultados()->delete();
                $servicioExamen->delete();
            }

            $numeroOrden = $servicio->numero_orden;
            $servicio->delete();

            DB::commit();

            Log::info('Servicio eliminado', [
                'numero_orden' => $numeroOrden,
                'usuario_id' => Auth::id(),
            ]);

            return redirect()->route('servicios.index')
                ->with('success', "Servicio {$numeroOrden} eliminado exitosamente.");

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al eliminar servicio', [
                'servicio_id' => $servicio->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Error al eliminar el servicio: '.$e->getMessage());
        }
    }

    public function registrarPago(Request $request, Servicio $servicio)
    {
        $request->validate([
            'monto' => ['required', 'numeric', 'min:0.01'],
            'medio_pago' => ['required', 'in:Efectivo,Tarjeta dÃ©bito,Tarjeta crÃ©dito,Transferencia,Nequi,Daviplata'],
        ], [
            'monto.required' => 'El monto es obligatorio.',
            'monto.numeric' => 'El monto debe ser un nÃºmero.',
            'monto.min' => 'El monto debe ser mayor a cero.',
            'medio_pago.required' => 'El medio de pago es obligatorio.',
            'medio_pago.in' => 'El medio de pago seleccionado no es vÃ¡lido.',
        ]);

        $nuevoValorPagado = $servicio->valor_pagado + $request->monto;

        if ($nuevoValorPagado > $servicio->valor_total) {
            return back()->with('error', 'El pago excede el valor total del servicio.');
        }

        $servicio->valor_pagado = $nuevoValorPagado;
        $servicio->medio_pago = $request->medio_pago;
        $servicio->estado_pago = $this->calcularEstadoPago($servicio->valor_total, $nuevoValorPagado);
        $servicio->save();

        return back()->with('success', 'Pago registrado exitosamente.');
    }

    public function asignarProfesional(Request $request, ServicioExamen $servicioExamen)
    {
        $request->validate([
            'profesional_id' => ['required', 'exists:profesionales,id'],
        ], [
            'profesional_id.required' => 'Debe seleccionar un profesional.',
            'profesional_id.exists' => 'El profesional seleccionado no existe.',
        ]);

        $servicioExamen->profesional_id = $request->profesional_id;
        $servicioExamen->save();

        return back()->with('success', 'Profesional asignado exitosamente.');
    }

    public function actualizarFechaTomaMuestra(ActualizarFechaTomaMuestraRequest $request, ServicioExamen $servicioExamen): \Illuminate\Http\RedirectResponse
    {
        if (! $servicioExamen->puedeEditarse()) {
            return back()->with('error', 'No se puede modificar la fecha de toma de muestra en el estado actual del examen.');
        }

        $servicioExamen->fecha_toma_muestra = $request->fecha_toma_muestra;
        $servicioExamen->save();

        return back()->with('success', 'Fecha de toma de muestra actualizada exitosamente.');
    }

    public function cambiarEstado(Request $request, ServicioExamen $servicioExamen)
    {
        $request->validate([
            'estado' => ['required', 'in:PENDIENTE,EN_PROCESO,COMPLETADO,VALIDADO,ENTREGADO'],
        ], [
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado seleccionado no es vÃ¡lido.',
        ]);

        $estadoAnterior = $servicioExamen->estado;
        $nuevoEstado = $request->estado;

        // Para exÃ¡menes remitidos, PENDIENTE â†’ ENTREGADO es una transiciÃ³n vÃ¡lida directa
        if ($servicioExamen->es_remitido && $nuevoEstado === 'ENTREGADO') {
            if ($estadoAnterior === 'ENTREGADO') {
                return back()->with('error', 'El examen ya fue entregado.');
            }
        } elseif (! $this->esTransicionValida($estadoAnterior, $nuevoEstado)) {
            return back()->with('error', 'TransiciÃ³n de estado no vÃ¡lida.');
        }

        $servicioExamen->estado = $nuevoEstado;

        // Actualizar fechas segÃºn el estado
        switch ($nuevoEstado) {
            case 'EN_PROCESO':
                if (! $servicioExamen->fecha_toma_muestra) {
                    $servicioExamen->fecha_toma_muestra = now();
                }
                break;
            case 'COMPLETADO':
                $servicioExamen->fecha_resultado = now();
                break;
            case 'VALIDADO':
                $servicioExamen->fecha_validacion = now();
                break;
            case 'ENTREGADO':
                $servicioExamen->fecha_entrega = now();
                break;
        }

        $servicioExamen->save();

        return back()->with('success', 'Estado actualizado exitosamente.');
    }

    public function asignarLaboratorio(Request $request, ServicioExamen $servicioExamen)
    {
        abort_if(! $servicioExamen->es_remitido, 403);
        abort_if($servicioExamen->estado === 'ENTREGADO', 403);

        $request->validate([
            'laboratorio_id' => ['required', 'exists:laboratorios,id'],
        ], [
            'laboratorio_id.required' => 'Debe seleccionar un laboratorio.',
            'laboratorio_id.exists' => 'El laboratorio seleccionado no existe.',
        ]);

        $le = LaboratorioExamen::where('laboratorio_id', $request->laboratorio_id)
            ->where('examen_id', $servicioExamen->examen_id)
            ->firstOrFail();

        $servicioExamen->update([
            'laboratorio_id' => $le->laboratorio_id,
            'costo_remision_snapshot' => $le->valor_remision,
        ]);

        return back()->with('success', 'Laboratorio asignado correctamente.');
    }

    private function generarNumeroOrden(): string
    {
        $fecha = now()->format('Ymd');
        $ultimoServicio = Servicio::whereDate('created_at', today())->latest()->first();

        $consecutivo = $ultimoServicio ? (int) substr($ultimoServicio->numero_orden, -4) + 1 : 1;

        return 'ORD-'.$fecha.'-'.str_pad($consecutivo, 4, '0', STR_PAD_LEFT);
    }

    private function calcularEstadoPago(float $total, float $pagado): string
    {
        if ($pagado == 0) {
            return 'PENDIENTE';
        } elseif ($pagado < $total) {
            return 'PARCIAL';
        } else {
            return 'PAGADO';
        }
    }

    private function esTransicionValida(string $estadoActual, string $estadoNuevo): bool
    {
        $transiciones = [
            'PENDIENTE' => ['EN_PROCESO'],
            'EN_PROCESO' => ['COMPLETADO', 'PENDIENTE'],
            'COMPLETADO' => ['VALIDADO', 'EN_PROCESO'],
            'VALIDADO' => ['ENTREGADO'],
            'ENTREGADO' => [],
        ];

        return in_array($estadoNuevo, $transiciones[$estadoActual] ?? []);
    }
}
