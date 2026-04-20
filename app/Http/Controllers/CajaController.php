<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CajaController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $fechaDesde = $request->filled('fecha_desde')
            ? $request->fecha_desde
            : now()->startOfMonth()->toDateString();

        $fechaHasta = $request->filled('fecha_hasta')
            ? $request->fecha_hasta
            : now()->toDateString();

        $query = Servicio::with(['cliente', 'serviciosExamen.examen'])
            ->whereDate('fecha', '>=', $fechaDesde)
            ->whereDate('fecha', '<=', $fechaHasta);

        if ($request->ajax()) {
            $servicios = $query->latest('fecha')->get();

            return response()->json([
                'data' => $servicios->map(function (Servicio $servicio) {
                    $totalExamenes = $servicio->serviciosExamen->count();
                    $remitidos = $servicio->serviciosExamen->where('es_remitido', true);
                    $totalRemitidos = $remitidos->count();
                    $costoRemision = $remitidos->sum(fn ($se) => $se->examen?->valor_remision ?? 0);
                    $gananciaNeta = $servicio->valor_total - $costoRemision;

                    $estadoPagoBadge = match ($servicio->estado_pago) {
                        'PENDIENTE' => '<span class="badge bg-warning text-dark">Pendiente</span>',
                        'PARCIAL' => '<span class="badge bg-info">Parcial</span>',
                        default => '<span class="badge bg-success">Pagado</span>',
                    };

                    return [
                        'numero_orden' => $servicio->numero_orden,
                        'servicio_id' => $servicio->id,
                        'fecha' => $servicio->fecha->format('d/m/Y'),
                        'cliente_nombre' => $servicio->cliente->nombre_completo,
                        'total_examenes' => $totalExamenes,
                        'total_remitidos' => $totalRemitidos,
                        'valor_facturado' => $servicio->valor_total,
                        'costo_remision' => $costoRemision,
                        'ganancia_neta' => $gananciaNeta,
                        'valor_cobrado' => $servicio->valor_pagado,
                        'estado_pago' => $estadoPagoBadge,
                    ];
                }),
            ]);
        }

        // Métricas agregadas para las tarjetas
        $servicios = $query->get();

        $totalOrdenes = $servicios->count();
        $totalExamenes = $servicios->sum(fn ($s) => $s->serviciosExamen->count());
        $totalRemitidos = $servicios->sum(fn ($s) => $s->serviciosExamen->where('es_remitido', true)->count());
        $valorFacturado = $servicios->sum('valor_total');
        $valorCobrado = $servicios->sum('valor_pagado');
        $saldoPendiente = $valorFacturado - $valorCobrado;
        $costoRemisiones = $servicios->sum(function (Servicio $servicio) {
            return $servicio->serviciosExamen
                ->where('es_remitido', true)
                ->sum(fn ($se) => $se->examen?->valor_remision ?? 0);
        });
        $gananciaNeta = $valorFacturado - $costoRemisiones;

        return view('caja.index', compact(
            'fechaDesde',
            'fechaHasta',
            'totalOrdenes',
            'totalExamenes',
            'totalRemitidos',
            'valorFacturado',
            'valorCobrado',
            'saldoPendiente',
            'costoRemisiones',
            'gananciaNeta',
        ));
    }
}
