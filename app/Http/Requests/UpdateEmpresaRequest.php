<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmpresaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'nit' => ['required', 'string', 'max:20'],
            'razon_social' => ['required', 'string', 'max:255'],
            'direccion' => ['required', 'string', 'max:255'],
            'barrio' => ['nullable', 'string', 'max:100'],
            'ciudad' => ['required', 'string', 'max:100'],
            'telefono_uno' => ['required', 'string', 'max:20'],
            'telefono_dos' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nit.required' => 'El NIT es obligatorio.',
            'nit.max' => 'El NIT no puede exceder 20 caracteres.',
            'razon_social.required' => 'La razón social es obligatoria.',
            'razon_social.max' => 'La razón social no puede exceder 255 caracteres.',
            'direccion.required' => 'La dirección es obligatoria.',
            'direccion.max' => 'La dirección no puede exceder 255 caracteres.',
            'barrio.max' => 'El barrio no puede exceder 100 caracteres.',
            'ciudad.required' => 'La ciudad es obligatoria.',
            'ciudad.max' => 'La ciudad no puede exceder 100 caracteres.',
            'telefono_uno.required' => 'El teléfono principal es obligatorio.',
            'telefono_uno.max' => 'El teléfono principal no puede exceder 20 caracteres.',
            'telefono_dos.max' => 'El teléfono secundario no puede exceder 20 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.max' => 'El correo electrónico no puede exceder 255 caracteres.',
            'logo.image' => 'El logo debe ser una imagen.',
            'logo.mimes' => 'El logo debe ser un archivo PNG, JPG o JPEG.',
            'logo.max' => 'El logo no puede exceder 2MB.',
        ];
    }

    /**
     * Get custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'nit' => 'NIT',
            'razon_social' => 'razón social',
            'direccion' => 'dirección',
            'barrio' => 'barrio',
            'ciudad' => 'ciudad',
            'telefono_uno' => 'teléfono principal',
            'telefono_dos' => 'teléfono secundario',
            'email' => 'correo electrónico',
            'logo' => 'logo',
        ];
    }
}
