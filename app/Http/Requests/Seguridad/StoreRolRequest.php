<?php

namespace App\Http\Requests\Seguridad;

use App\Http\Requests\BaseRequest;

class StoreRolRequest extends BaseRequest
{
    /**
     * Reglas para registrar un rol.
     */
    public function rules(): array
    {
        return [

            'nombre' => [
                'required',
                'string',
                'max:100',
                'unique:rol,nombre'
            ],

            'descripcion' => [
                'nullable',
                'string',
                'max:255'
            ]

        ];
    }
}