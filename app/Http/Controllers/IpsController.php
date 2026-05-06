<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIpsRequest;
use App\Http\Requests\UpdateIpsRequest;
use App\Models\Ips;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IpsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $ipsList = Ips::withCount('clientes')
                ->orderBy('razon_social')
                ->get()
                ->map(function ($ips) {
                    return [
                        'id'                 => $ips->id,
                        'razon_social'       => $ips->razon_social,
                        'nit'                => $ips->nit,
                        'correo_electronico' => $ips->correo_electronico,
                        'logo'               => $ips->logo,
                        'clientes_count'     => $ips->clientes_count,
                    ];
                });

            return response()->json(['data' => $ipsList]);
        }

        return view('ips.index');
    }

    public function create()
    {
        return view('ips.create');
    }

    public function store(StoreIpsRequest $request)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('logo')) {
                $data['logo'] = $request->file('logo')->store('ips', 'public');
            }

            Ips::create($data);

            return redirect()->route('ips.index')
                ->with('success', 'IPS registrada exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al registrar la IPS: '.$e->getMessage());
        }
    }

    public function edit(Ips $ip)
    {
        return view('ips.edit', compact('ip'));
    }

    public function update(UpdateIpsRequest $request, Ips $ip)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('logo')) {
                if ($ip->logo) {
                    Storage::disk('public')->delete($ip->logo);
                }
                $data['logo'] = $request->file('logo')->store('ips', 'public');
            }

            $ip->update($data);

            return redirect()->route('ips.index')
                ->with('success', 'IPS actualizada exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al actualizar la IPS: '.$e->getMessage());
        }
    }

    public function destroy(Ips $ip)
    {
        try {
            if ($ip->clientes()->count() > 0) {
                return redirect()->route('ips.index')
                    ->with('error', 'No se puede eliminar la IPS porque tiene pacientes asociados.');
            }

            if ($ip->logo) {
                Storage::disk('public')->delete($ip->logo);
            }

            $ip->delete();

            return redirect()->route('ips.index')
                ->with('success', 'IPS eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('ips.index')
                ->with('error', 'Error al eliminar la IPS: '.$e->getMessage());
        }
    }
}
