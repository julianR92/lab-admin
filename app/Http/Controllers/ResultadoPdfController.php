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
            'cliente.ips',
            'serviciosExamen' => function ($query) {
                $query->whereIn('estado', ['VALIDADO', 'ENTREGADO'])
                    ->where('es_remitido', false)
                    ->with([
                        'examen.categoria',
                        'examen.parametros' => function ($q) {
                            $q->where('status', 1)->orderBy('orden');
                        },
                        'examen.parametros.valoresReferencia' => function ($q) {
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
        $pdf->setOption('defaultFont', 'Carlito');
        $pdf->setOption('fontDir', storage_path('fonts'));
        $pdf->setOption('fontCache', storage_path('fonts'));

        $fontMetrics = $pdf->getDomPDF()->getFontMetrics();
        $fontMetrics->registerFont(['family' => 'Carlito', 'style' => 'normal', 'weight' => 'normal'], storage_path('fonts/Carlito-Regular.ttf'));
        $fontMetrics->registerFont(['family' => 'Carlito', 'style' => 'normal', 'weight' => 'bold'], storage_path('fonts/Carlito-Bold.ttf'));
        $fontMetrics->registerFont(['family' => 'Carlito', 'style' => 'italic', 'weight' => 'normal'], storage_path('fonts/Carlito-Italic.ttf'));
        $fontMetrics->registerFont(['family' => 'Carlito', 'style' => 'italic', 'weight' => 'bold'], storage_path('fonts/Carlito-BoldItalic.ttf'));

        // Generar nombre del archivo
        $nombreArchivo = 'Resultado_'.$servicio->cliente->documento.'_'.$servicio->cliente->nombre.'_'.$servicio->cliente->apellido.'.pdf';

        return $pdf->stream($nombreArchivo);
    }

    public function generarPdfIndividual(Request $request, $servicioId, $servicioExamenId)
    {
        // Cargar servicio con todas sus relaciones y filtrar por el examen específico
        $servicio = Servicio::with([
            'cliente.ips',
            'serviciosExamen' => function ($query) use ($servicioExamenId) {
                $query->where('id', $servicioExamenId)
                    ->whereIn('estado', ['VALIDADO', 'ENTREGADO'])
                    ->where('es_remitido', false)
                    ->with([
                        'examen.categoria',
                        'examen.parametros' => function ($q) {
                            $q->where('status', 1)->orderBy('orden');
                        },
                        'examen.parametros.valoresReferencia' => function ($q) {
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

        // Verificar que el examen esté validado
        if ($servicio->serviciosExamen->isEmpty()) {
            abort(404, 'El examen solicitado no está validado o no existe');
        }

        // Obtener datos de la empresa
        $empresa = Empresa::first();

        // Preparar datos para el PDF
        $data = [
            'servicio' => $servicio,
            'empresa' => $empresa,
            'examenesAgrupados' => null,
            'fecha_generacion' => now(),
        ];

        // Configurar PDF
        $pdf = Pdf::loadView('servicios.resultado-pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('isPhpEnabled', true);
        $pdf->setOption('defaultFont', 'Carlito');
        $pdf->setOption('fontDir', storage_path('fonts'));
        $pdf->setOption('fontCache', storage_path('fonts'));

        $fontMetrics = $pdf->getDomPDF()->getFontMetrics();
        $fontMetrics->registerFont(['family' => 'Carlito', 'style' => 'normal', 'weight' => 'normal'], storage_path('fonts/Carlito-Regular.ttf'));
        $fontMetrics->registerFont(['family' => 'Carlito', 'style' => 'normal', 'weight' => 'bold'], storage_path('fonts/Carlito-Bold.ttf'));
        $fontMetrics->registerFont(['family' => 'Carlito', 'style' => 'italic', 'weight' => 'normal'], storage_path('fonts/Carlito-Italic.ttf'));
        $fontMetrics->registerFont(['family' => 'Carlito', 'style' => 'italic', 'weight' => 'bold'], storage_path('fonts/Carlito-BoldItalic.ttf'));

        // Obtener nombre del examen para el archivo
        $examen = $servicio->serviciosExamen->first();
        $nombreExamen = str_replace(' ', '_', $examen->examen->nombre);
        $nombreExamen = str_replace('/', '-', $nombreExamen);
        $nombreArchivo = 'Resultado_'.$nombreExamen.'_'.$servicio->cliente->documento.'_'.$servicio->cliente->nombre.'_'.$servicio->cliente->apellido.'.pdf';

        return $pdf->stream($nombreArchivo);
    }
}


