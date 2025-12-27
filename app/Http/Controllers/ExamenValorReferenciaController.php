<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExamenValorReferenciaRequest;
use App\Http\Requests\UpdateExamenValorReferenciaRequest;
use App\Models\ExamenValorReferencia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ExamenValorReferenciaController extends Controller
{
    /**
     * Almacenar un nuevo valor de referencia.
     */
    public function store(StoreExamenValorReferenciaRequest $request): RedirectResponse
    {
        try {
            $datos = $request->validated();

            // Los booleans ya vienen convertidos desde prepareForValidation()
            $datos['status'] = $datos['status'] ? 1 : 0;

            // Limpiar campos según tipo_referencia
            switch ($datos['tipo_referencia']) {
                case 'RANGO':
                    $datos['valor_cualitativo'] = null;
                    $datos['categoria'] = null;
                    $datos['operador'] = null;
                    break;

                case 'CUALITATIVO':
                    $datos['valor_min'] = null;
                    $datos['valor_max'] = null;
                    $datos['categoria'] = null;
                    $datos['operador'] = null;
                    break;

                case 'CATEGORIZADO':
                    $datos['valor_cualitativo'] = null;
                    break;

                case 'INFORMATIVO':
                    $datos['valor_min'] = null;
                    $datos['valor_max'] = null;
                    $datos['valor_cualitativo'] = null;
                    $datos['categoria'] = null;
                    $datos['operador'] = null;
                    break;
            }

            $valorReferencia = ExamenValorReferencia::create($datos);

            return redirect()->route('examenes.show', $valorReferencia->examen_id)
                ->with('success', 'Valor de referencia creado exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al crear el valor de referencia: '.$e->getMessage());
        }
    }

    /**
     * Obtener los datos de un valor de referencia para edición (AJAX).
     */
    public function edit(ExamenValorReferencia $examenValorReferencia): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'valorReferencia' => $examenValorReferencia,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el valor de referencia: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Actualizar un valor de referencia existente.
     */
    public function update(UpdateExamenValorReferenciaRequest $request, ExamenValorReferencia $examenValorReferencia): RedirectResponse
    {
        try {
            $datos = $request->validated();

            // Los booleans ya vienen convertidos desde prepareForValidation()
            $datos['status'] = $datos['status'] ? 1 : 0;

            // Limpiar campos según tipo_referencia
            switch ($datos['tipo_referencia']) {
                case 'RANGO':
                    $datos['valor_cualitativo'] = null;
                    $datos['categoria'] = null;
                    $datos['operador'] = null;
                    break;

                case 'CUALITATIVO':
                    $datos['valor_min'] = null;
                    $datos['valor_max'] = null;
                    $datos['categoria'] = null;
                    $datos['operador'] = null;
                    break;

                case 'CATEGORIZADO':
                    $datos['valor_cualitativo'] = null;
                    break;

                case 'INFORMATIVO':
                    $datos['valor_min'] = null;
                    $datos['valor_max'] = null;
                    $datos['valor_cualitativo'] = null;
                    $datos['categoria'] = null;
                    $datos['operador'] = null;
                    break;
            }

            $examenValorReferencia->update($datos);

            return redirect()->route('examenes.show', $examenValorReferencia->examen_id)
                ->with('success', 'Valor de referencia actualizado exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al actualizar el valor de referencia: '.$e->getMessage());
        }
    }

    /**
     * Eliminar un valor de referencia.
     */
    public function destroy(ExamenValorReferencia $examenValorReferencia): RedirectResponse
    {
        try {
            // Verificar si tiene resultados asociados (solo si está asociado a un parámetro)
            if ($examenValorReferencia->parametro_id && $examenValorReferencia->resultados()->exists()) {
                return back()->with('error', 'No se puede eliminar este valor de referencia porque tiene resultados de exámenes asociados.');
            }

            $examenId = $examenValorReferencia->examen_id;
            $examenValorReferencia->delete();

            return redirect()->route('examenes.show', $examenId)
                ->with('success', 'Valor de referencia eliminado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el valor de referencia: '.$e->getMessage());
        }
    }

    /**
     * Obtener los tipos de referencia disponibles.
     */
    public static function getTiposReferencia(): array
    {
        return [
            'RANGO' => 'Rango (Min-Max)',
            'CUALITATIVO' => 'Cualitativo (Positivo/Negativo)',
            'CATEGORIZADO' => 'Categorizado (Por categorías)',
            'INFORMATIVO' => 'Informativo (Solo descripción)',
        ];
    }
}
