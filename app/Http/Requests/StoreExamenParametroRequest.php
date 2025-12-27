<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExamenParametroRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'status' => $this->has('status'),
            'requerido' => $this->has('requerido'),
            'es_calculado' => $this->has('es_calculado'),
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
            'examen_id' => 'required|exists:examen,id',
            'nombre_parametro' => 'required|string|max:255',
            'seccion' => 'nullable|string|max:100|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'codigo_parametro' => 'required|string|max:50|regex:/^[A-Z0-9_]+$/|unique:examen_parametros,codigo_parametro,NULL,id,examen_id,'.$this->examen_id,
            'tipo_dato' => 'required|in:DECIMAL,INTEGER,TEXT,SELECT',
            'unidad_medida' => 'nullable|string|max:50',
            'decimales' => 'nullable|integer|min:0|max:4',
            'orden' => 'required|integer|min:1',
            'es_calculado' => 'nullable|boolean',
            'formula_calculo' => 'nullable|array',
            'formula_calculo.formula' => 'required_if:es_calculado,1|nullable|string',
            'formula_calculo.parametros' => 'required_if:es_calculado,1|nullable|array',
            'formula_calculo.descripcion' => 'nullable|string',
            'requerido' => 'nullable|boolean',
            'opciones_select' => 'required_if:tipo_dato,SELECT|array',
            'status' => 'required|boolean',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'examen_id' => 'examen',
            'nombre_parametro' => 'nombre del parámetro',
            'seccion' => 'sección',
            'codigo_parametro' => 'código del parámetro',
            'tipo_dato' => 'tipo de dato',
            'unidad_medida' => 'unidad de medida',
            'decimales' => 'cantidad de decimales',
            'orden' => 'orden',
            'es_calculado' => 'es calculado',
            'formula_calculo' => 'fórmula de cálculo',
            'formula_calculo.formula' => 'fórmula',
            'formula_calculo.parametros' => 'parámetros de la fórmula',
            'formula_calculo.descripcion' => 'descripción de la fórmula',
            'requerido' => 'requerido',
            'opciones_select' => 'opciones del select',
            'status' => 'estado',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'examen_id.required' => 'El examen es obligatorio.',
            'examen_id.exists' => 'El examen seleccionado no existe.',
            'nombre_parametro.required' => 'El nombre del parámetro es obligatorio.',
            'seccion.regex' => 'La sección solo puede contener letras y espacios.',
            'codigo_parametro.required' => 'El código del parámetro es obligatorio.',
            'codigo_parametro.regex' => 'El código solo puede contener letras mayúsculas, números y guiones bajos (sin espacios).',
            'codigo_parametro.unique' => 'Ya existe un parámetro con este código en el examen.',
            'tipo_dato.required' => 'El tipo de dato es obligatorio.',
            'tipo_dato.in' => 'El tipo de dato debe ser DECIMAL, INTEGER, TEXT o SELECT.',
            'orden.required' => 'El orden es obligatorio.',
            'formula_calculo.formula.required_if' => 'La fórmula es obligatoria cuando el parámetro es calculado.',
            'formula_calculo.parametros.required_if' => 'Los parámetros de la fórmula son obligatorios cuando el parámetro es calculado.',
            'opciones_select.required_if' => 'Las opciones son obligatorias cuando el tipo de dato es SELECT.',
        ];
    }
}
