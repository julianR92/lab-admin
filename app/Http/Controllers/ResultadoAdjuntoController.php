<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResultadoAdjuntoRequest;
use App\Models\ResultadoAdjunto;
use App\Models\ServicioExamen;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class ResultadoAdjuntoController extends Controller
{
    /**
     * Obtener todos los adjuntos de un servicio examen
     */
    public function index(ServicioExamen $servicioExamen): JsonResponse
    {
        $adjuntos = $servicioExamen->adjuntos()
            ->with('usuario')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $adjuntos,
            'total' => $adjuntos->count(),
        ], 200, [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * Subir una imagen al servidor
     */
    public function store(StoreResultadoAdjuntoRequest $request, ServicioExamen $servicioExamen): JsonResponse
    {
        // Verificar que el examen no esté entregado
        if ($servicioExamen->estado === 'ENTREGADO') {
            return response()->json([
                'success' => false,
                'message' => 'No se pueden adjuntar imágenes a exámenes entregados',
            ], 403);
        }

        // Verificar límite de 3 imágenes por examen
        $cantidadActual = $servicioExamen->adjuntos()->count();
        if ($cantidadActual >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'Se ha alcanzado el límite máximo de 3 imágenes por examen',
            ], 403);
        }

        try {
            $archivo = $request->file('archivo');

            // Obtener número de orden para organizar carpetas
            $numeroOrden = $servicioExamen->servicio->numero_orden;
            $carpeta = "examenes/{$numeroOrden}";

            // Sanitizar y generar nombre único
            $nombreOriginal = $archivo->getClientOriginalName();
            $extension = $archivo->getClientOriginalExtension();
            $nombreSanitizado = Str::slug(pathinfo($nombreOriginal, PATHINFO_FILENAME));
            $timestamp = now()->format('YmdHis');
            $random = Str::random(6);
            $nombreFinal = "{$timestamp}_{$random}_{$nombreSanitizado}.{$extension}";

            // Guardar archivo primero (fuera de la transacción)
            $rutaArchivo = $archivo->storeAs($carpeta, $nombreFinal, 'public');

            if (! $rutaArchivo) {
                throw new \Exception('No se pudo guardar el archivo en el servidor');
            }

            // Verificar que el archivo se guardó correctamente
            if (! Storage::disk('public')->exists($rutaArchivo)) {
                throw new \Exception('El archivo no se guardó correctamente');
            }

            DB::beginTransaction();

            // Obtener el profesional del servicio examen o el primero disponible
            $profesionalId = $servicioExamen->profesional_id ?? \App\Models\Profesional::first()?->id;

            if (! $profesionalId) {
                throw new \Exception('No hay profesional disponible para asignar el adjunto');
            }

            // Crear registro en BD
            $adjunto = ResultadoAdjunto::create([
                'servicio_examen_id' => $servicioExamen->id,
                'tipo_archivo' => 'IMAGEN',
                'nombre_archivo' => $nombreOriginal,
                'ruta_archivo' => $rutaArchivo,
                'mime_type' => $archivo->getMimeType(),
                'tamano_bytes' => $archivo->getSize(),
                'descripcion' => $request->input('descripcion'),
                'orden' => ResultadoAdjunto::where('servicio_examen_id', $servicioExamen->id)->count() + 1,
                'subido_por' => $profesionalId,
            ]);

            // Recargar relaciones
            $adjunto->load('usuario');

            DB::commit();

            Log::info('Adjunto subido exitosamente', [
                'adjunto_id' => $adjunto->id,
                'servicio_examen_id' => $servicioExamen->id,
                'usuario_id' => Auth::id(),
                'tamano' => $adjunto->tamano_bytes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Imagen subida exitosamente',
                'data' => $adjunto,
            ], 201, [], JSON_UNESCAPED_SLASHES);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al subir adjunto', [
                'error' => $e->getMessage(),
                'servicio_examen_id' => $servicioExamen->id,
                'usuario_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al subir la imagen: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Eliminar una imagen
     */
    public function destroy(ServicioExamen $servicioExamen, ResultadoAdjunto $adjunto): JsonResponse
    {        // Verificar que el examen no esté entregado
        if ($servicioExamen->estado === 'ENTREGADO') {
            return response()->json([
                'success' => false,
                'message' => 'No se pueden eliminar imágenes de exámenes entregados',
            ], 403);
        }
        try {
            // Verificar que el adjunto pertenece al servicio examen
            if ($adjunto->servicio_examen_id !== $servicioExamen->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'El adjunto no pertenece a este examen',
                ], 403);
            }

            DB::beginTransaction();

            // Eliminar archivo físico
            if (Storage::disk('public')->exists($adjunto->ruta_archivo)) {
                Storage::disk('public')->delete($adjunto->ruta_archivo);
            }

            // Eliminar registro
            $adjunto->delete();

            DB::commit();

            Log::info('Adjunto eliminado', [
                'adjunto_id' => $adjunto->id,
                'usuario_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Imagen eliminada exitosamente',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al eliminar adjunto', [
                'error' => $e->getMessage(),
                'adjunto_id' => $adjunto->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la imagen: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Descargar una imagen individual
     */
    public function download(ServicioExamen $servicioExamen, ResultadoAdjunto $adjunto)
    {
        // Verificar que el adjunto pertenece al servicio examen
        if ($adjunto->servicio_examen_id !== $servicioExamen->id) {
            abort(403, 'El adjunto no pertenece a este examen');
        }

        // Verificar que el archivo existe
        if (! Storage::disk('public')->exists($adjunto->ruta_archivo)) {
            abort(404, 'Archivo no encontrado');
        }

        return Storage::disk('public')->download(
            $adjunto->ruta_archivo,
            $adjunto->nombre_archivo
        );
    }

    /**
     * Descargar todas las imágenes como ZIP
     */
    public function downloadAll(ServicioExamen $servicioExamen)
    {
        try {
            $adjuntos = $servicioExamen->adjuntos;

            if ($adjuntos->isEmpty()) {
                abort(404, 'No hay imágenes para descargar');
            }

            // Crear archivo ZIP temporal
            $zipFileName = 'examenes_'.$servicioExamen->servicio->numero_orden.'_'.now()->format('Ymd_His').'.zip';
            $zipPath = storage_path('app/temp/'.$zipFileName);

            // Crear directorio temp si no existe
            if (! file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            // Verificar si la extensión ZipArchive está disponible
            if (! class_exists('ZipArchive')) {
                abort(500, 'La extensión ZIP no está disponible en el servidor');
            }

            $zip = new ZipArchive;

            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                $archivosSumados = 0;

                foreach ($adjuntos as $adjunto) {
                    $rutaCompleta = Storage::disk('public')->path($adjunto->ruta_archivo);

                    if (file_exists($rutaCompleta)) {
                        // Usar nombre único si hay duplicados
                        $nombreArchivo = $adjunto->nombre_archivo;
                        $contador = 1;

                        while ($zip->locateName($nombreArchivo) !== false) {
                            $info = pathinfo($adjunto->nombre_archivo);
                            $nombreArchivo = $info['filename'].'_'.$contador.'.'.$info['extension'];
                            $contador++;
                        }

                        $zip->addFile($rutaCompleta, $nombreArchivo);
                        $archivosSumados++;
                    }
                }

                $zip->close();

                if ($archivosSumados === 0) {
                    @unlink($zipPath);
                    abort(404, 'No se encontraron archivos físicos para descargar');
                }

                // Descargar y eliminar archivo temporal
                return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
            }

            abort(500, 'Error al crear el archivo ZIP');

        } catch (\Exception $e) {
            Log::error('Error al descargar ZIP', [
                'error' => $e->getMessage(),
                'servicio_examen_id' => $servicioExamen->id,
                'trace' => $e->getTraceAsString(),
            ]);

            abort(500, 'Error al descargar las imágenes: '.$e->getMessage());
        }
    }

    /**
     * Actualizar orden de imágenes
     */
    public function updateOrden(Request $request, ServicioExamen $servicioExamen): JsonResponse
    {
        try {
            $orden = $request->input('orden', []);

            DB::beginTransaction();

            foreach ($orden as $index => $adjuntoId) {
                ResultadoAdjunto::where('id', $adjuntoId)
                    ->where('servicio_examen_id', $servicioExamen->id)
                    ->update(['orden' => $index + 1]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Orden actualizado exitosamente',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            // Si hay error después de guardar el archivo, eliminarlo
            if (isset($rutaArchivo) && Storage::disk('public')->exists($rutaArchivo)) {
                Storage::disk('public')->delete($rutaArchivo);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el orden',
            ], 500);
        }
    }
}
