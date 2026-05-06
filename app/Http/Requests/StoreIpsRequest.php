<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIpsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'razon_social'       => ['required', 'string', 'max:255'],
            'nit'                => ['required', 'string', 'max:50', 'unique:ips,nit'],
            'correo_electronico' => ['required', 'email', 'max:150'],
            'logo'               => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'razon_social.required'       => 'La razón social es obligatoria.',
            'razon_social.max'            => 'La razón social no puede tener más de 255 caracteres.',
            'nit.required'                => 'El NIT es obligatorio.',
            'nit.unique'                  => 'Este NIT ya está registrado.',
            'nit.max'                     => 'El NIT no puede tener más de 50 caracteres.',
            'correo_electronico.required' => 'El correo electrónico es obligatorio.',
            'correo_electronico.email'    => 'El correo electrónico no es válido.',
            'correo_electronico.max'      => 'El correo electrónico no puede tener más de 150 caracteres.',
            'logo.image'                  => 'El logo debe ser una imagen.',
            'logo.mimes'                  => 'El logo debe ser PNG, JPG o JPEG.',
            'logo.max'                    => 'El logo no puede pesar más de 2MB.',
        ];
    }
}
