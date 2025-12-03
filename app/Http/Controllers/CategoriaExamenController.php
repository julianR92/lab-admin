<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoriaExamenRequest;
use App\Http\Requests\UpdateCategoriaExamenRequest;
use App\Models\CategoriaExamen;
use Illuminate\Http\Request;

class CategoriaExamenController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $categorias = CategoriaExamen::orderBy('orden')
                ->get()
                ->map(function ($categoria) {
                    return [
                        'id' => $categoria->id,
                        'categoria' => $categoria->categoria,
                        'descripcion' => $categoria->descripcion,
                        'status' => $categoria->status,
                        'orden' => $categoria->orden,
                        'examenes_count' => $categoria->examenes()->count(),
                    ];
                });

            return response()->json(['data' => $categorias]);
        }

        return view('categorias-examen.index');
    }

    public function create()
    {
        $ultimoOrden = CategoriaExamen::max('orden') ?? 0;

        return view('categorias-examen.create', compact('ultimoOrden'));
    }

    public function store(StoreCategoriaExamenRequest $request)
    {
        try {
            CategoriaExamen::create($request->validated());

            return redirect()->route('categorias-examen.index')
                ->with('success', 'Categoría creada exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al crear la categoría: '.$e->getMessage());
        }
    }

    public function show(CategoriaExamen $categoriasExaman)
    {
        $categoriasExaman->load('examenes');

        return view('categorias-examen.show', compact('categoriasExaman'));
    }

    public function edit(CategoriaExamen $categoriasExaman)
    {
        return view('categorias-examen.edit', compact('categoriasExaman'));
    }

    public function update(UpdateCategoriaExamenRequest $request, CategoriaExamen $categoriasExaman)
    {
        try {
            $categoriasExaman->update($request->validated());

            return redirect()->route('categorias-examen.index')
                ->with('success', 'Categoría actualizada exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al actualizar la categoría: '.$e->getMessage());
        }
    }

    public function destroy(CategoriaExamen $categoriasExaman)
    {
        try {
            if ($categoriasExaman->examenes()->count() > 0) {
                return redirect()->route('categorias-examen.index')
                    ->with('error', 'No se puede eliminar una categoría con exámenes asociados.');
            }

            $categoriasExaman->delete();

            return redirect()->route('categorias-examen.index')
                ->with('success', 'Categoría eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('categorias-examen.index')
                ->with('error', 'Error al eliminar la categoría: '.$e->getMessage());
        }
    }
}
