<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfesionalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $profesionalId = $this->route('profesionale');

        return [
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'documento' => ['required', 'string', 'max:20', Rule::unique('profesionales', 'documento')->ignore($profesionalId)],
            'profesion' => ['required', 'string', 'max:100'],
            'registro_profesional' => ['required', 'string', 'max:50', Rule::unique('profesionales', 'registro_profesional')->ignore($profesionalId)],
            'especialidad' => ['nullable', 'string', 'max:150'],
            'firma_digital' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'status' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede tener más de 100 caracteres.',
            'apellido.required' => 'El apellido es obligatorio.',
            'apellido.max' => 'El apellido no puede tener más de 100 caracteres.',
            'documento.required' => 'El documento es obligatorio.',
            'documento.unique' => 'Este documento ya está registrado.',
            'documento.max' => 'El documento no puede tener más de 20 caracteres.',
            'profesion.required' => 'La profesión es obligatoria.',
            'profesion.max' => 'La profesión no puede tener más de 100 caracteres.',
            'registro_profesional.required' => 'El registro profesional es obligatorio.',
            'registro_profesional.unique' => 'Este registro profesional ya existe.',
            'registro_profesional.max' => 'El registro profesional no puede tener más de 50 caracteres.',
            'especialidad.max' => 'La especialidad no puede tener más de 150 caracteres.',
            'firma_digital.image' => 'La firma debe ser una imagen.',
            'firma_digital.mimes' => 'La firma debe ser PNG, JPG o JPEG.',
            'firma_digital.max' => 'La firma no puede pesar más de 2MB.',
            'telefono.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'email.email' => 'El correo electrónico no es válido.',
            'email.max' => 'El correo electrónico no puede tener más de 150 caracteres.',
            'status.required' => 'El estado es obligatorio.',
            'status.boolean' => 'El estado debe ser activo o inactivo.',
        ];
    }
}
