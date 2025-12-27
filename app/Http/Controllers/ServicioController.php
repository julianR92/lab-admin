<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServicioRequest;
use App\Http\Requests\UpdateServicioRequest;
use App\Models\Cliente;
use App\Models\Examen;
use App\Models\Profesional;
use App\Models\Servicio;
use App\Models\ServicioExamen;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        if ($request->filled('estado_pago')) {
            $query->where('estado_pago', $request->estado_pago);
        }

        if ($request->filled('buscar')) {
            $termino = $request->buscar;
            $query->where(function ($q) use ($termino) {
                $q->whereHas('cliente', function ($clienteQuery) use ($termino) {
                    $clienteQuery->where('nombre', 'like', "%{$termino}%")
                        ->orWhere('apellido', 'like', "%{$termino}%")
                        ->orWhere('documento', 'like', "%{$termino}%");
                });
            });
        }

        $servicios = $query->latest('fecha')->paginate(15);

        return view('servicios.index', compact('servicios'));
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

            // Generar número de orden
            $numeroOrden = $this->generarNumeroOrden();

            // Crear servicio
            $servicio = Servicio::create([
                'cliente_id' => $request->cliente_id,
                'numero_orden' => $numeroOrden,
                'fecha' => $request->fecha,
                'valor_total' => $valorTotal,
                'valor_pagado' => $valorPagado,
                'medio_pago' => $request->medio_pago,
                'estado_pago' => $estadoPago,
                'observaciones' => $request->observaciones,
            ]);

            // Crear servicios_examen
            foreach ($request->examenes as $index => $examenId) {
                ServicioExamen::create([
                    'servicio_id' => $servicio->id,
                    'examen_id' => $examenId,
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
            'cliente',
            'serviciosExamen.examen.categoria',
            'serviciosExamen.profesional',
            'serviciosExamen.resultados',
        ]);

        $profesionales = Profesional::where('status', 1)
            ->where('profesion', 'Bacteriólogo')
            ->orderBy('nombre')
            ->get();

        return view('servicios.show', compact('servicio', 'profesionales'));
    }

    public function edit(Servicio $servicio)
    {
        // Verificar que no tenga exámenes con resultados
        $tieneResultados = $servicio->serviciosExamen()
            ->whereIn('estado', ['COMPLETADO', 'VALIDADO', 'ENTREGADO'])
            ->exists();

        if ($tieneResultados) {
            return back()->with('error', 'No se puede editar un servicio con exámenes que ya tienen resultados.');
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

            // Si se están actualizando exámenes, verificar que no tengan resultados
            if ($request->has('examenes')) {
                $tieneResultados = $servicio->serviciosExamen()
                    ->whereIn('estado', ['COMPLETADO', 'VALIDADO', 'ENTREGADO'])
                    ->exists();

                if ($tieneResultados) {
                    return back()->with('error', 'No se pueden modificar exámenes que ya tienen resultados.');
                }

                // Eliminar exámenes actuales solo si están pendientes
                $servicio->serviciosExamen()->where('estado', 'PENDIENTE')->delete();

                // Agregar nuevos exámenes
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
        // Verificar que todos los exámenes estén pendientes
        $tieneExamenesEnProceso = $servicio->serviciosExamen()
            ->where('estado', '!=', 'PENDIENTE')
            ->exists();

        if ($tieneExamenesEnProceso) {
            return back()->with('error', 'No se puede eliminar el servicio porque tiene exámenes en proceso.');
        }

        try {
            $servicio->delete();

            return redirect()->route('servicios.index')
                ->with('success', 'Servicio eliminado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el servicio: '.$e->getMessage());
        }
    }

    public function registrarPago(Request $request, Servicio $servicio)
    {
        $request->validate([
            'monto' => ['required', 'numeric', 'min:0.01'],
            'medio_pago' => ['required', 'in:Efectivo,Tarjeta débito,Tarjeta crédito,Transferencia,Nequi,Daviplata'],
        ], [
            'monto.required' => 'El monto es obligatorio.',
            'monto.numeric' => 'El monto debe ser un número.',
            'monto.min' => 'El monto debe ser mayor a cero.',
            'medio_pago.required' => 'El medio de pago es obligatorio.',
            'medio_pago.in' => 'El medio de pago seleccionado no es válido.',
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

    public function cambiarEstado(Request $request, ServicioExamen $servicioExamen)
    {
        $request->validate([
            'estado' => ['required', 'in:PENDIENTE,EN_PROCESO,COMPLETADO,VALIDADO,ENTREGADO'],
        ], [
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado seleccionado no es válido.',
        ]);

        $estadoAnterior = $servicioExamen->estado;
        $nuevoEstado = $request->estado;

        // Validar transición de estado
        if (! $this->esTransicionValida($estadoAnterior, $nuevoEstado)) {
            return back()->with('error', 'Transición de estado no válida.');
        }

        $servicioExamen->estado = $nuevoEstado;

        // Actualizar fechas según el estado
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
