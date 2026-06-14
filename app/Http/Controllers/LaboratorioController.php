<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLaboratorioRequest;
use App\Http\Requests\UpdateLaboratorioRequest;
use App\Models\Examen;
use App\Models\Laboratorio;
use App\Models\LaboratorioExamen;
use Illuminate\Http\Request;

class LaboratorioController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $labs = Laboratorio::withCount('serviciosExamen')
                ->get()
                ->map(function (Laboratorio $lab) {
                    return [
                        'id' => $lab->id,
                        'nombre' => $lab->nombre,
                        'nit' => $lab->nit ?? '—',
                        'ciudad' => $lab->ciudad ?? '—',
                        'email' => $lab->email ?? '—',
                        'telefono' => $lab->telefono ?? '—',
                        'examenes_count' => $lab->examenes()->count(),
                        'servicios_examen_count' => $lab->servicios_examen_count,
                        'status' => $lab->status,
                    ];
                });

            return response()->json(['data' => $labs]);
        }

        return view('laboratorios.index');
    }

    public function create()
    {
        return view('laboratorios.create');
    }

    public function show(Laboratorio $laboratorio)
    {
        $examenesConfigurados = LaboratorioExamen::where('laboratorio_id', $laboratorio->id)
            ->with('examen.categoria')
            ->orderBy('created_at', 'desc')
            ->get();

        $examenesDisponibles = Examen::activos()
            ->whereNotIn('id', $examenesConfigurados->pluck('examen_id'))
            ->orderBy('nombre')
            ->get();

        return view('laboratorios.show', compact('laboratorio', 'examenesConfigurados', 'examenesDisponibles'));
    }

    public function store(StoreLaboratorioRequest $request)
    {
        try {
            Laboratorio::create($request->validated() + ['status' => $request->boolean('status', true)]);

            return redirect()->route('laboratorios.index')
                ->with('success', 'Laboratorio registrado exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al registrar el laboratorio: '.$e->getMessage());
        }
    }

    public function edit(Laboratorio $laboratorio)
    {
        return view('laboratorios.edit', compact('laboratorio'));
    }

    public function update(UpdateLaboratorioRequest $request, Laboratorio $laboratorio)
    {
        try {
            $data = $request->validated();
            $data['status'] = $request->boolean('status', true);
            $laboratorio->update($data);

            return redirect()->route('laboratorios.index')
                ->with('success', 'Laboratorio actualizado exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al actualizar el laboratorio: '.$e->getMessage());
        }
    }

    public function destroy(Laboratorio $laboratorio)
    {
        try {
            if ($laboratorio->serviciosExamen()->count() > 0) {
                return redirect()->route('laboratorios.index')
                    ->with('error', 'No se puede eliminar el laboratorio porque tiene exámenes de servicio asociados.');
            }

            $laboratorio->delete();

            return redirect()->route('laboratorios.index')
                ->with('success', 'Laboratorio eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('laboratorios.index')
                ->with('error', 'Error al eliminar el laboratorio: '.$e->getMessage());
        }
    }

    public function storeExamen(Request $request, Laboratorio $laboratorio)
    {
        $request->validate([
            'examen_id' => ['required', 'exists:examen,id'],
            'valor_remision' => ['required', 'numeric', 'min:0'],
        ], [
            'examen_id.required' => 'Debe seleccionar un examen.',
            'examen_id.exists' => 'El examen seleccionado no existe.',
            'valor_remision.required' => 'El valor de remisión es obligatorio.',
            'valor_remision.numeric' => 'El valor de remisión debe ser numérico.',
            'valor_remision.min' => 'El valor de remisión no puede ser negativo.',
        ]);

        LaboratorioExamen::updateOrCreate(
            ['laboratorio_id' => $laboratorio->id, 'examen_id' => $request->examen_id],
            ['valor_remision' => $request->valor_remision]
        );

        return redirect()->route('laboratorios.show', $laboratorio)
            ->with('success', 'Examen agregado al laboratorio.');
    }

    public function destroyExamen(Laboratorio $laboratorio, Examen $examen)
    {
        LaboratorioExamen::where('laboratorio_id', $laboratorio->id)
            ->where('examen_id', $examen->id)
            ->delete();

        return redirect()->route('laboratorios.show', $laboratorio)
            ->with('success', 'Examen eliminado del laboratorio.');
    }
}
