<?php

namespace App\Http\Requests\Seguridad;

use App\Http\Requests\BaseRequest;

class ActualizarFotoPerfilRequest extends BaseRequest
{
    /**
     * Reglas para actualizar
     * la fotografía de perfil.
     */
    public function rules(): array
    {
        return [

            'foto' => [

                'required',

                'image',

                'mimes:jpg,jpeg,png,webp',

                'max:2048'

            ]

        ];
    }


    /**
     * Mensajes personalizados.
     */
    public function messages(): array
    {
        return [

            'foto.image' =>
                'El archivo seleccionado debe ser una imagen.',

            'foto.mimes' =>
                'La fotografía debe ser JPG, JPEG, PNG o WEBP.',

            'foto.max' =>
                'La fotografía no puede superar los 2 MB.'

        ];
    }
}
