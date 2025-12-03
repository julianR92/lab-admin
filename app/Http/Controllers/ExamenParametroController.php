<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExamenParametroRequest;
use App\Http\Requests\UpdateExamenParametroRequest;
use App\Models\ExamenParametro;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ExamenParametroController extends Controller
{
    /**
     * Almacenar un nuevo parámetro.
     */
    public function store(StoreExamenParametroRequest $request): RedirectResponse
    {
        try {

            $datos = $request->validated();

            // Los booleans ya vienen convertidos desde prepareForValidation()
            // Solo convertir a 1/0 para la base de datos
            $datos['status'] = $datos['status'] ? 1 : 0;
            $datos['es_calculado'] = $datos['es_calculado'] ? 1 : 0;
            $datos['requerido'] = $datos['requerido'] ? 1 : 0;

            // Limpiar formula_calculo si no es calculado
            if (! $datos['es_calculado']) {
                $datos['formula_calculo'] = null;
            }

            // Limpiar opciones_select si no es SELECT
            if ($datos['tipo_dato'] !== 'SELECT') {
                $datos['opciones_select'] = null;
            }

            // Limpiar decimales si no es DECIMAL
            if ($datos['tipo_dato'] !== 'DECIMAL') {
                $datos['decimales'] = null;
            }
            $parametro = ExamenParametro::create($datos);

            return redirect()->route('examenes.show', $parametro->examen_id)
                ->with('success', 'Parámetro creado exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al crear el parámetro: '.$e->getMessage());
        }
    }

    /**
     * Obtener los datos de un parámetro para edición (AJAX).
     */
    public function edit(ExamenParametro $examenParametro): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'parametro' => $examenParametro,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el parámetro: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Actualizar un parámetro existente.
     */
    public function update(UpdateExamenParametroRequest $request, ExamenParametro $examenParametro): RedirectResponse
    {
        try {
            $datos = $request->validated();

            // Los booleans ya vienen convertidos desde prepareForValidation()
            // Solo convertir a 1/0 para la base de datos
            $datos['status'] = $datos['status'] ? 1 : 0;
            $datos['es_calculado'] = $datos['es_calculado'] ? 1 : 0;
            $datos['requerido'] = $datos['requerido'] ? 1 : 0;

            // Limpiar formula_calculo si no es calculado o si está vacío
            if (! $datos['es_calculado']) {
                $datos['formula_calculo'] = null;
            } elseif (isset($datos['formula_calculo']) && (empty($datos['formula_calculo']['formula']) && empty($datos['formula_calculo']['parametros']))) {
                $datos['formula_calculo'] = null;
            }

            // Limpiar opciones_select si no es SELECT
            if ($datos['tipo_dato'] !== 'SELECT') {
                $datos['opciones_select'] = null;
            }

            // Limpiar decimales si no es DECIMAL
            if ($datos['tipo_dato'] !== 'DECIMAL') {
                $datos['decimales'] = null;
            }

            $examenParametro->update($datos);

            return redirect()->route('examenes.show', $examenParametro->examen_id)
                ->with('success', 'Parámetro actualizado exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al actualizar el parámetro: '.$e->getMessage());
        }
    }

    /**
     * Eliminar un parámetro.
     */
    public function destroy(ExamenParametro $examenParametro): RedirectResponse
    {
        try {
            // Verificar si tiene valores de referencia asociados
            if ($examenParametro->valoresReferencia()->exists()) {
                return back()->with('error', 'No se puede eliminar el parámetro porque tiene valores de referencia asociados.');
            }

            // Verificar si tiene resultados asociados
            if ($examenParametro->resultados()->exists()) {
                return back()->with('error', 'No se puede eliminar el parámetro porque tiene resultados asociados.');
            }

            $examenId = $examenParametro->examen_id;
            $examenParametro->delete();

            return redirect()->route('examenes.show', $examenId)
                ->with('success', 'Parámetro eliminado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el parámetro: '.$e->getMessage());
        }
    }

    /**
     * Obtener los tipos de dato disponibles.
     */
    public static function getTiposDato(): array
    {
        return [
            'DECIMAL' => 'Decimal (con decimales)',
            'INTEGER' => 'Entero (sin decimales)',
            'TEXT' => 'Texto libre',
            'SELECT' => 'Opciones predefinidas (SELECT)',
        ];
    }
}
