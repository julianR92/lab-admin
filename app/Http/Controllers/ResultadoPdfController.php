<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Servicio;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ResultadoPdfController extends Controller
{
    public function generarPdf(Request $request, $servicioId)
    {
        // Cargar servicio con todas sus relaciones
        $servicio = Servicio::with([
            'cliente',
            'serviciosExamen' => function ($query) {
                $query->whereIn('estado', ['VALIDADO', 'ENTREGADO'])
                    ->with([
                        'examen.categoria',
                        'examen.parametros' => function ($q) {
                            $q->where('status', 1)->orderBy('orden');
                        },
                        'profesional',
                        'resultados' => function ($q) {
                            $q->with(['parametro', 'valorReferencia'])->orderBy('id');
                        },
                        'adjuntos' => function ($q) {
                            $q->orderBy('orden');
                        },
                    ]);
            },
        ])->findOrFail($servicioId);




        // Verificar que al menos un examen esté validado
        if ($servicio->serviciosExamen->isEmpty()) {
            abort(404, 'No hay resultados validados para generar el PDF');
        }

        // Obtener datos de la empresa
        $empresa = Empresa::first();

        // Agrupar servicios de examen por categoría si se solicita
        $agruparPorCategoria = $request->boolean('agrupar', false);

        if ($agruparPorCategoria) {
            $examenesAgrupados = $servicio->serviciosExamen->groupBy(function ($se) {
                return $se->examen->categoria->categoria ?? 'Sin categoría';
            });
        } else {
            $examenesAgrupados = null;
        }

        // Preparar datos para el PDF
        $data = [
            'servicio' => $servicio,
            'empresa' => $empresa,
            'examenesAgrupados' => $examenesAgrupados,
            'fecha_generacion' => now(),
        ];

        // Configurar PDF
        $pdf = Pdf::loadView('servicios.resultado-pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('isPhpEnabled', true);
        $pdf->setOption('defaultFont', 'DejaVu Sans');

        // Generar nombre del archivo
        $nombreArchivo = 'Resultado_'.$servicio->cliente->documento.'_'.$servicio->cliente->nombre.'_'.$servicio->cliente->apellido.'.pdf';

        return $pdf->stream($nombreArchivo);
    }
}
