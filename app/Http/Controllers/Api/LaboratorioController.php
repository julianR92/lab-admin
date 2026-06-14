<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Examen;
use App\Models\LaboratorioExamen;
use Illuminate\Http\JsonResponse;

class LaboratorioController extends Controller
{
    public function porExamen(Examen $examen): JsonResponse
    {
        $labs = LaboratorioExamen::where('examen_id', $examen->id)
            ->whereHas('laboratorio', fn ($q) => $q->where('status', true))
            ->with('laboratorio')
            ->get()
            ->map(fn ($le) => [
                'id' => $le->laboratorio->id,
                'nombre' => $le->laboratorio->nombre,
                'valor_remision' => (float) $le->valor_remision,
            ]);

        return response()->json($labs);
    }
}
