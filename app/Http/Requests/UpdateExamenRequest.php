<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExamenRequest extends FormRequest
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
            'requiere_ayuno' => $this->has('requiere_ayuno'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $examenId = $this->route('examene');

        return [
            'categoria_id' => ['required', 'exists:categoria_examen,id'],
            'codigo' => ['required', 'string', 'max:20', Rule::unique('examen', 'codigo')->ignore($examenId)],
            'nombre' => ['required', 'string', 'max:255'],
            'tipo_resultado' => [
                'required',
                'in:NUMERICO_SIMPLE,NUMERICO_CATEGORIZADO,CUALITATIVO_SIMPLE,CUALITATIVO_REACTIVO,CUALITATIVO_MULTIPLE_OPCIONES,MULTIPLE_CALCULADO,TABLA_HEMATOLOGIA,TEXTO_DESCRIPTIVO',
            ],
            'unidad_medida' => ['nullable', 'string', 'max:50'],
            'tecnica' => ['nullable', 'string', 'max:255'],
            'muestra_requerida' => ['nullable', 'string', 'max:100'],
            'valor_total' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'valor_remision' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'tiempo_entrega' => ['required', 'integer', 'min:1', 'max:720'],
            'requiere_ayuno' => ['nullable', 'boolean'],
            'instrucciones_paciente' => ['nullable', 'string'],
            'status' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'categoria_id.required' => 'La categoría es obligatoria.',
            'categoria_id.exists' => 'La categoría seleccionada no existe.',
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique' => 'El código ya está registrado.',
            'codigo.max' => 'El código no puede exceder 20 caracteres.',
            'nombre.required' => 'El nombre del examen es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
            'tipo_resultado.required' => 'El tipo de resultado es obligatorio.',
            'tipo_resultado.in' => 'El tipo de resultado seleccionado no es válido.',
            'unidad_medida.max' => 'La unidad de medida no puede exceder 50 caracteres.',
            'tecnica.max' => 'La técnica no puede exceder 255 caracteres.',
            'muestra_requerida.max' => 'La muestra requerida no puede exceder 100 caracteres.',
            'valor_total.required' => 'El valor total es obligatorio.',
            'valor_total.numeric' => 'El valor total debe ser un número.',
            'valor_total.min' => 'El valor total no puede ser negativo.',
            'valor_remision.numeric' => 'El valor de remisión debe ser un número.',
            'valor_remision.min' => 'El valor de remisión no puede ser negativo.',
            'tiempo_entrega.required' => 'El tiempo de entrega es obligatorio.',
            'tiempo_entrega.integer' => 'El tiempo de entrega debe ser un número entero.',
            'tiempo_entrega.min' => 'El tiempo de entrega debe ser al menos 1 hora.',
            'tiempo_entrega.max' => 'El tiempo de entrega no puede exceder 720 horas (30 días).',
        ];
    }

    /**
     * Get custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'categoria_id' => 'categoría',
            'codigo' => 'código',
            'nombre' => 'nombre',
            'tipo_resultado' => 'tipo de resultado',
            'unidad_medida' => 'unidad de medida',
            'tecnica' => 'técnica',
            'muestra_requerida' => 'muestra requerida',
            'valor_total' => 'valor total',
            'valor_remision' => 'valor de remisión',
            'tiempo_entrega' => 'tiempo de entrega',
            'requiere_ayuno' => 'requiere ayuno',
            'instrucciones_paciente' => 'instrucciones al paciente',
            'status' => 'estado',
        ];
    }
}
