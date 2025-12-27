<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExamenValorReferenciaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->has('status'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'examen_id' => ['required', 'integer', 'exists:examen,id'],
            'parametro_id' => ['nullable', 'integer', 'exists:examen_parametros,id'],
            'tipo_referencia' => ['required', 'in:RANGO,CUALITATIVO,CATEGORIZADO,INFORMATIVO'],

            // Contexto
            'genero' => ['nullable', 'in:M,F'],
            'edad_min' => ['nullable', 'integer', 'min:0', 'max:120'],
            'edad_max' => ['nullable', 'integer', 'min:0', 'max:120', 'gte:edad_min'],
            'condicion_especial' => ['nullable', 'string', 'max:255'],

            // Valores según tipo_referencia
            'valor_min' => [
                Rule::requiredIf(fn () => in_array($this->tipo_referencia, ['RANGO', 'CATEGORIZADO'])),
                'nullable',
                'numeric',
            ],
            'valor_max' => [
                Rule::requiredIf(fn () => in_array($this->tipo_referencia, ['RANGO', 'CATEGORIZADO'])),
                'nullable',
                'numeric',
                'gte:valor_min',
            ],
            'operador' => ['nullable', 'string', 'max:10'],
            'valor_cualitativo' => [
                Rule::requiredIf(fn () => $this->tipo_referencia === 'CUALITATIVO'),
                'nullable',
                'string',
                'max:255',
            ],
            'categoria' => [
                Rule::requiredIf(fn () => $this->tipo_referencia === 'CATEGORIZADO'),
                'nullable',
                'string',
                'max:100',
            ],

            // Otros campos
            'descripcion' => ['nullable', 'string', 'max:500'],
            'orden' => ['required', 'integer', 'min:1', 'max:999'],
            'status' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'examen_id.required' => 'El examen es requerido.',
            'examen_id.exists' => 'El examen seleccionado no existe.',
            'parametro_id.exists' => 'El parámetro seleccionado no existe.',
            'tipo_referencia.required' => 'El tipo de referencia es requerido.',
            'tipo_referencia.in' => 'El tipo de referencia debe ser RANGO, CUALITATIVO, CATEGORIZADO o INFORMATIVO.',

            'genero.in' => 'El género debe ser M (Masculino) o F (Femenino).',
            'edad_min.integer' => 'La edad mínima debe ser un número entero.',
            'edad_min.min' => 'La edad mínima no puede ser menor a 0.',
            'edad_min.max' => 'La edad mínima no puede ser mayor a 120.',
            'edad_max.integer' => 'La edad máxima debe ser un número entero.',
            'edad_max.min' => 'La edad máxima no puede ser menor a 0.',
            'edad_max.max' => 'La edad máxima no puede ser mayor a 120.',
            'edad_max.gte' => 'La edad máxima debe ser mayor o igual a la edad mínima.',
            'condicion_especial.max' => 'La condición especial no puede exceder 255 caracteres.',

            'valor_min.numeric' => 'El valor mínimo debe ser numérico.',
            'valor_max.numeric' => 'El valor máximo debe ser numérico.',
            'valor_max.gte' => 'El valor máximo debe ser mayor o igual al valor mínimo.',
            'operador.max' => 'El operador no puede exceder 10 caracteres.',
            'valor_cualitativo.max' => 'El valor cualitativo no puede exceder 255 caracteres.',
            'categoria.max' => 'La categoría no puede exceder 100 caracteres.',

            'descripcion.max' => 'La descripción no puede exceder 500 caracteres.',
            'orden.required' => 'El orden es requerido.',
            'orden.integer' => 'El orden debe ser un número entero.',
            'orden.min' => 'El orden debe ser al menos 1.',
            'orden.max' => 'El orden no puede ser mayor a 999.',
        ];
    }
}
