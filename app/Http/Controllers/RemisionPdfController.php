<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRemisionPdfRequest;
use App\Models\ServicioExamen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RemisionPdfController extends Controller
{
    public function store(StoreRemisionPdfRequest $request, ServicioExamen $servicioExamen): RedirectResponse
    {
        abort_unless($servicioExamen->es_remitido, 403, 'Este examen no está marcado como remitido.');
        abort_if($servicioExamen->estado === 'ENTREGADO', 403, 'No se puede modificar un examen ya entregado.');

        // Eliminar archivo anterior si existe
        if ($servicioExamen->pdf_remision && Storage::disk('public')->exists($servicioExamen->pdf_remision)) {
            Storage::disk('public')->delete($servicioExamen->pdf_remision);
        }

        $numeroOrden = $servicioExamen->servicio->numero_orden;
        $timestamp = now()->format('YmdHis');
        $carpeta = "examenes/{$numeroOrden}";
        $nombreArchivo = "remision_{$timestamp}.pdf";

        $ruta = $request->file('archivo')->storeAs($carpeta, $nombreArchivo, 'public');

        $servicioExamen->update(['pdf_remision' => $ruta]);

        return back()->with('success', 'PDF de remisión subido exitosamente.');
    }

    public function destroy(ServicioExamen $servicioExamen): RedirectResponse
    {
        abort_unless($servicioExamen->pdf_remision, 404, 'No hay PDF de remisión para eliminar.');

        if (Storage::disk('public')->exists($servicioExamen->pdf_remision)) {
            Storage::disk('public')->delete($servicioExamen->pdf_remision);
        }

        $servicioExamen->update(['pdf_remision' => null]);

        return back()->with('success', 'PDF de remisión eliminado.');
    }

    public function download(ServicioExamen $servicioExamen): StreamedResponse
    {
        abort_unless($servicioExamen->pdf_remision, 404, 'No hay PDF de remisión disponible.');
        abort_unless(Storage::disk('public')->exists($servicioExamen->pdf_remision), 404, 'El archivo no existe.');

        $nombreDescarga = 'Remision_'
            .$servicioExamen->examen->codigo.'_'
            .$servicioExamen->servicio->cliente->documento.'.pdf';

        return Storage::disk('public')->download($servicioExamen->pdf_remision, $nombreDescarga);
    }
}
