<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEmpresaRequest;
use App\Models\Empresa;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends Controller
{
    /**
     * Mostrar el formulario de edición de la empresa.
     */
    public function edit()
    {
        $empresa = Empresa::obtenerEmpresa();

        // Si no existe el registro, crearlo con datos por defecto
        if (! $empresa) {
            $empresa = Empresa::create([
                'nit' => '',
                'razon_social' => 'Laboratorio Clínico',
                'direccion' => '',
                'barrio' => '',
                'ciudad' => '',
                'telefono_uno' => '',
                'telefono_dos' => '',
                'email' => '',
                'logo' => null,
            ]);
        }

        return view('empresa.edit', compact('empresa'));
    }

    /**
     * Actualizar la información de la empresa.
     */
    public function update(UpdateEmpresaRequest $request)
    {
        $empresa = Empresa::obtenerEmpresa();

        // Si no existe el registro, crearlo
        if (! $empresa) {
            $empresa = new Empresa;
        }

        $datos = $request->validated();

        // Manejar la subida del logo
        if ($request->hasFile('logo')) {
            // Eliminar logo anterior si existe
            if ($empresa->logo && Storage::disk('public')->exists($empresa->logo)) {
                Storage::disk('public')->delete($empresa->logo);
            }

            // Guardar nuevo logo
            $rutaLogo = $request->file('logo')->store('logos', 'public');
            $datos['logo'] = $rutaLogo;
        }

        $empresa->fill($datos);
        $empresa->save();

        return redirect()->route('empresa.edit')
            ->with('success', 'Información de la empresa actualizada correctamente.');
    }

    /**
     * Eliminar el logo de la empresa.
     */
    public function deleteLogo()
    {
        $empresa = Empresa::obtenerEmpresa();

        if ($empresa && $empresa->logo) {
            // Eliminar archivo físico
            if (Storage::disk('public')->exists($empresa->logo)) {
                Storage::disk('public')->delete($empresa->logo);
            }

            // Actualizar registro
            $empresa->logo = null;
            $empresa->save();

            return back()->with('success', 'Logo eliminado correctamente.');
        }

        return back()->with('error', 'No hay logo para eliminar.');
    }
}
