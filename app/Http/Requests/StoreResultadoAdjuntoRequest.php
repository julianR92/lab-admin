<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResultadoAdjuntoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // La autorización se maneja en el controlador
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'archivo' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,gif,webp',
                'max:10240', // 10 MB en KB
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'archivo.required' => 'Debe seleccionar un archivo',
            'archivo.file' => 'El archivo no es válido',
            'archivo.mimes' => 'El archivo debe ser una imagen (JPG, JPEG, PNG, GIF, WEBP)',
            'archivo.max' => 'El tamaño máximo permitido es de 10 MB',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
