<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoriaExamenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoriaId = $this->route('categorias_examan');

        return [
            'categoria' => ['required', 'string', 'max:100', Rule::unique('categoria_examen', 'categoria')->ignore($categoriaId)],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'boolean'],
            'orden' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'categoria.required' => 'El nombre de la categoría es obligatorio.',
            'categoria.unique' => 'Esta categoría ya existe.',
            'categoria.max' => 'El nombre de la categoría no puede tener más de 100 caracteres.',
            'descripcion.max' => 'La descripción no puede tener más de 500 caracteres.',
            'status.required' => 'El estado es obligatorio.',
            'status.boolean' => 'El estado debe ser activo o inactivo.',
            'orden.required' => 'El orden es obligatorio.',
            'orden.integer' => 'El orden debe ser un número entero.',
            'orden.min' => 'El orden debe ser mayor o igual a 1.',
        ];
    }
}
