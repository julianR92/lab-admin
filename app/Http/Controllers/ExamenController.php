<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExamenRequest;
use App\Http\Requests\UpdateExamenRequest;
use App\Models\CategoriaExamen;
use App\Models\Examen;
use Illuminate\Http\Request;

class ExamenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $examenes = Examen::with('categoria')
                ->select('examen.*')
                ->get()
                ->map(function ($examen) {
                    return [
                        'id' => $examen->id,
                        'codigo' => $examen->codigo,
                        'nombre' => $examen->nombre,
                        'categoria' => $examen->categoria?->categoria ?? 'Sin categoría',
                        'tipo_resultado' => $examen->tipo_resultado,
                        'tipo_resultado_label' => $this->getTipoResultadoLabel($examen->tipo_resultado),
                        'valor_total' => number_format($examen->valor_total, 0, ',', '.'),
                        'valor_total_raw' => $examen->valor_total,
                        'tiempo_entrega' => $examen->tiempo_entrega,
                        'requiere_ayuno' => $examen->requiere_ayuno,
                        'parametros_count' => $examen->parametros()->count(),
                        'valores_referencia_count' => $examen->valoresReferencia()->count(),
                        'status' => $examen->status,
                    ];
                });

            return response()->json(['data' => $examenes]);
        }

        return view('examenes.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorias = CategoriaExamen::activas()->ordenadas()->get();
        $tiposResultado = $this->getTiposResultado();

        return view('examenes.create', compact('categorias', 'tiposResultado'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExamenRequest $request)
    {
        try {
            $datos = $request->validated();
            //$datos['status'] = $request->has('status') ? 1 : 0;
            //$datos['requiere_ayuno'] = $request->has('requiere_ayuno') ? 1 : 0;

            $examen = Examen::create($datos);

            return redirect()->route('examenes.show', $examen)
                ->with('success', 'Examen creado exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al crear el examen: '.$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Examen $examene)
    {
        $examene->load(['categoria', 'parametros', 'valoresReferencia']);

        return view('examenes.show', ['examen' => $examene]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Examen $examene)
    {
        $categorias = CategoriaExamen::activas()->ordenadas()->get();
        $tiposResultado = $this->getTiposResultado();

        return view('examenes.edit', [
            'examen' => $examene,
            'categorias' => $categorias,
            'tiposResultado' => $tiposResultado,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExamenRequest $request, Examen $examene)
    {
        try {
            $datos = $request->validated();
            //$datos['status'] = $request->has('status') ? 1 : 0;
            //$datos['requiere_ayuno'] = $request->has('requiere_ayuno') ? 1 : 0;
            $examene->update($datos);

            return redirect()->route('examenes.show', $examene)
                ->with('success', 'Examen actualizado exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al actualizar el examen: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Examen $examene)
    {
        try {
            // Verificar si tiene servicios asociados
            if ($examene->serviciosExamen()->exists()) {
                return back()->with('error', 'No se puede eliminar el examen porque tiene servicios asociados.');
            }

            $examene->delete();

            return redirect()->route('examenes.index')
                ->with('success', 'Examen eliminado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el examen: '.$e->getMessage());
        }
    }

    /**
     * Get tipos de resultado con sus labels.
     */
    private function getTiposResultado(): array
    {
        return [
            'NUMERICO_SIMPLE' => 'Numérico Simple',
            'NUMERICO_CATEGORIZADO' => 'Numérico Categorizado',
            'CUALITATIVO_SIMPLE' => 'Cualitativo Simple',
            'CUALITATIVO_REACTIVO' => 'Cualitativo Reactivo',
            'CUALITATIVO_MULTIPLE_OPCIONES' => 'Cualitativo Múltiples Opciones',
            'MULTIPLE_CALCULADO' => 'Múltiple Calculado',
            'TABLA_HEMATOLOGIA' => 'Tabla Hematología',
            'TEXTO_DESCRIPTIVO' => 'Texto Descriptivo',
        ];
    }

    /**
     * Get label for tipo_resultado.
     */
    private function getTipoResultadoLabel(string $tipo): string
    {
        $tipos = $this->getTiposResultado();

        return $tipos[$tipo] ?? $tipo;
    }
}
