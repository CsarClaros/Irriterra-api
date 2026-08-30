<?php

namespace App\Http\Requests\Seguridad;

use App\Http\Requests\BaseRequest;

class StorePermisoRequest extends BaseRequest
{
    /**
     * Reglas para registrar un permiso.
     */
    public function rules(): array
    {
        return [

            'nombre' => [

                'required',

                'string',

                'max:150',

                'unique:permiso,nombre'

            ],

            'descripcion' => [

                'nullable',

                'string',

                'max:255'

            ]

        ];
    }
}