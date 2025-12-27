<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function buscar(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $clientes = Cliente::where(function ($q) use ($query) {
            $q->where('nombre', 'like', "%{$query}%")
                ->orWhere('apellido', 'like', "%{$query}%")
                ->orWhere('documento', 'like', "%{$query}%");
        })
            ->limit(10)
            ->get()
            ->map(function ($cliente) {
                return [
                    'id' => $cliente->id,
                    'nombre_completo' => $cliente->nombre_completo,
                    'documento' => $cliente->documento,
                    'tipo_documento' => $cliente->tipo_documento,
                    'edad' => $cliente->edad,
                    'genero' => $cliente->genero,
                    'telefono' => $cliente->telefono,
                    'email' => $cliente->email,
                    'eps' => $cliente->eps,
                ];
            });

        return response()->json($clientes);
    }
}
