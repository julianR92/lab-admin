<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActualizarFechaTomaMuestraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'fecha_toma_muestra' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_toma_muestra.required' => 'La fecha de toma de muestra es obligatoria.',
            'fecha_toma_muestra.date' => 'La fecha de toma de muestra no es válida.',
        ];
    }
}
