<?php

namespace App\Http\Requests\Seguridad;

use App\Http\Requests\BaseRequest;

class CambiarContrasenaRequest extends BaseRequest
{
    /**
     * Reglas para cambiar la contraseña.
     */
    public function rules(): array
    {
        return [

            'contrasena_actual' => [
                'required',
                'string',
                'max:255'
            ],

            'nueva_contrasena' => [
                'required',
                'string',
                'min:8',
                'max:255',
                'different:contrasena_actual'
            ],

            'nueva_contrasena_confirmation' => [
                'required',
                'string',
                'same:nueva_contrasena'
            ]

        ];
    }

    /**
     * Mensajes personalizados.
     */
    public function messages(): array
    {
        return [

            'contrasena_actual.required' =>
                'Debe ingresar su contraseña actual.',

            'nueva_contrasena.required' =>
                'Debe ingresar una nueva contraseña.',

            'nueva_contrasena.min' =>
                'La nueva contraseña debe contener al menos 8 caracteres.',

            'nueva_contrasena.different' =>
                'La nueva contraseña debe ser diferente de la contraseña actual.',

            'nueva_contrasena_confirmation.required' =>
                'Debe confirmar la nueva contraseña.',

            'nueva_contrasena_confirmation.same' =>
                'La confirmación de la nueva contraseña no coincide.'

        ];
    }
}
