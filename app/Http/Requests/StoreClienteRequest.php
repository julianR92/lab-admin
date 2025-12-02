<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'tipo_documento' => ['required', 'in:CC,TI,CE,PA,RC'],
            'documento' => ['required', 'string', 'max:20', 'unique:clientes,documento'],
            'genero' => ['nullable', 'in:M,F,O'],
            'fecha_nacimiento' => ['required', 'date', 'before:today', 'after_or_equal:'.now()->subYears(95)->format('Y-m-d')],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'eps' => ['nullable', 'string', 'max:150'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede tener más de 100 caracteres.',
            'apellido.required' => 'El apellido es obligatorio.',
            'apellido.max' => 'El apellido no puede tener más de 100 caracteres.',
            'tipo_documento.required' => 'El tipo de documento es obligatorio.',
            'tipo_documento.in' => 'El tipo de documento no es válido.',
            'documento.required' => 'El número de documento es obligatorio.',
            'documento.unique' => 'Este documento ya está registrado.',
            'documento.max' => 'El documento no puede tener más de 20 caracteres.',
            'genero.in' => 'El género seleccionado no es válido.',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.date' => 'La fecha de nacimiento no es válida.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'fecha_nacimiento.after_or_equal' => 'La edad no puede ser mayor a 95 años.',
            'telefono.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'email.email' => 'El correo electrónico no es válido.',
            'email.max' => 'El correo electrónico no puede tener más de 150 caracteres.',
            'direccion.max' => 'La dirección no puede tener más de 255 caracteres.',
            'ciudad.max' => 'La ciudad no puede tener más de 100 caracteres.',
            'eps.max' => 'La EPS no puede tener más de 150 caracteres.',
        ];
    }
}
