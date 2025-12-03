<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfesionalRequest;
use App\Http\Requests\UpdateProfesionalRequest;
use App\Models\Profesional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfesionalController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $profesionales = Profesional::withCount('serviciosExamen')
                ->orderBy('nombre')
                ->get()
                ->map(function ($profesional) {
                    return [
                        'id' => $profesional->id,
                        'nombre_completo' => $profesional->nombre_completo,
                        'documento' => $profesional->documento,
                        'profesion' => $profesional->profesion,
                        'registro_profesional' => $profesional->registro_profesional,
                        'especialidad' => $profesional->especialidad,
                        'telefono' => $profesional->telefono,
                        'email' => $profesional->email,
                        'status' => $profesional->status,
                        'servicios_count' => $profesional->servicios_examen_count,
                    ];
                });

            return response()->json(['data' => $profesionales]);
        }

        return view('profesionales.index');
    }

    public function create()
    {
        return view('profesionales.create');
    }

    public function store(StoreProfesionalRequest $request)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('firma_digital')) {
                $data['firma_digital'] = $request->file('firma_digital')->store('firmas', 'public');
            }

            Profesional::create($data);

            return redirect()->route('profesionales.index')
                ->with('success', 'Profesional registrado exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al registrar el profesional: '.$e->getMessage());
        }
    }

    public function show(Profesional $profesionale)
    {
        $profesionale->loadCount(['serviciosExamen', 'resultadosValidados']);

        return view('profesionales.show', compact('profesionale'));
    }

    public function edit(Profesional $profesionale)
    {
        return view('profesionales.edit', compact('profesionale'));
    }

    public function update(UpdateProfesionalRequest $request, Profesional $profesionale)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('firma_digital')) {
                if ($profesionale->firma_digital) {
                    Storage::disk('public')->delete($profesionale->firma_digital);
                }
                $data['firma_digital'] = $request->file('firma_digital')->store('firmas', 'public');
            }

            $profesionale->update($data);

            return redirect()->route('profesionales.index')
                ->with('success', 'Profesional actualizado exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al actualizar el profesional: '.$e->getMessage());
        }
    }

    public function destroy(Profesional $profesionale)
    {
        try {
            if ($profesionale->serviciosExamen()->count() > 0 || $profesionale->resultadosValidados()->count() > 0) {
                return redirect()->route('profesionales.index')
                    ->with('error', 'No se puede eliminar un profesional con servicios o resultados asociados.');
            }

            if ($profesionale->firma_digital) {
                Storage::disk('public')->delete($profesionale->firma_digital);
            }

            $profesionale->delete();

            return redirect()->route('profesionales.index')
                ->with('success', 'Profesional eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('profesionales.index')
                ->with('error', 'Error al eliminar el profesional: '.$e->getMessage());
        }
    }
}
