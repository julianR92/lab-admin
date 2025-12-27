<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServicioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => ['sometimes', 'required', 'exists:clientes,id'],
            'fecha' => ['sometimes', 'required', 'date'],
            'valor_pagado' => ['nullable', 'numeric', 'min:0'],
            'medio_pago' => ['nullable', 'in:Efectivo,Tarjeta débito,Tarjeta crédito,Transferencia,Nequi,Daviplata'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
            'examenes' => ['sometimes', 'array', 'min:1'],
            'examenes.*' => ['sometimes', 'exists:examen,id'],
            'precios' => ['sometimes', 'array', 'min:1'],
            'precios.*' => ['sometimes', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.required' => 'Debe seleccionar un cliente.',
            'cliente_id.exists' => 'El cliente seleccionado no existe.',
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha no es válida.',
            'valor_pagado.numeric' => 'El valor pagado debe ser un número.',
            'valor_pagado.min' => 'El valor pagado no puede ser negativo.',
            'medio_pago.in' => 'El medio de pago seleccionado no es válido.',
            'observaciones.max' => 'Las observaciones no pueden tener más de 1000 caracteres.',
            'examenes.array' => 'Los exámenes deben ser un arreglo.',
            'examenes.min' => 'Debe tener al menos un examen.',
            'examenes.*.exists' => 'Uno o más exámenes seleccionados no existen.',
            'precios.array' => 'Los precios deben ser un arreglo.',
            'precios.min' => 'Debe tener al menos un precio.',
            'precios.*.numeric' => 'Cada precio debe ser un número.',
            'precios.*.min' => 'Los precios no pueden ser negativos.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('examenes') && is_string($this->examenes)) {
            $this->merge([
                'examenes' => json_decode($this->examenes, true) ?? [],
            ]);
        }

        if ($this->has('precios') && is_string($this->precios)) {
            $this->merge([
                'precios' => json_decode($this->precios, true) ?? [],
            ]);
        }
    }
}
