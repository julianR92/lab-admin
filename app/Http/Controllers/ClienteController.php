<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Cliente::query();

            if ($request->has('search') && $request->search['value']) {
                $search = $request->search['value'];
                $query->buscar($search);
            }

            $clientes = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'data' => $clientes->map(function ($cliente) {
                    return [
                        'id' => $cliente->id,
                        'nombre_completo' => $cliente->nombre_completo,
                        'documento' => $cliente->tipo_documento.' '.$cliente->documento,
                        'genero' => $cliente->genero,
                        'edad' => $cliente->edad.' años',
                        'telefono' => $cliente->telefono ?? 'N/A',
                        'ciudad' => $cliente->ciudad ?? 'N/A',
                        'eps' => $cliente->eps ?? 'N/A',
                        'acciones' => $cliente->id,
                    ];
                }),
            ]);
        }

        return view('clientes.index');
    }

    public function create(): View
    {
        return view('clientes.create');
    }

    public function store(StoreClienteRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $data['edad'] = \Carbon\Carbon::parse($data['fecha_nacimiento'])->age;

            Cliente::create($data);

            return redirect()->route('clientes.index')
                ->with('success', 'Cliente registrado exitosamente');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al registrar el cliente: '.$e->getMessage());
        }
    }

    public function show(Cliente $cliente): View
    {
        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente): View
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente): RedirectResponse
    {
        try {
            $data = $request->validated();
            $data['edad'] = \Carbon\Carbon::parse($data['fecha_nacimiento'])->age;

            $cliente->update($data);

            return redirect()->route('clientes.index')
                ->with('success', 'Cliente actualizado exitosamente');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al actualizar el cliente: '.$e->getMessage());
        }
    }

    public function destroy(Cliente $cliente): RedirectResponse
    {
        try {
            $cliente->delete();

            return redirect()->route('clientes.index')
                ->with('success', 'Cliente eliminado exitosamente');
        } catch (\Exception $e) {
            return redirect()->route('clientes.index')
                ->with('error', 'No se puede eliminar el cliente porque tiene servicios asociados');
        }
    }
}
