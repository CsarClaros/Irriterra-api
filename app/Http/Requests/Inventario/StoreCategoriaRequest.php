<?php

namespace App\Http\Requests\Inventario;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class StoreCategoriaRequest extends BaseRequest
{
    /**
     * Reglas de validación.
     */
    public function rules(): array
    {
        return [

            'nombre' => [

                'required',

                'string',

                'max:150',

                Rule::unique('categoria','nombre')

            ],

            'descripcion' => [

                'nullable',

                'string',

                'max:255'

            ],

            'observaciones' => [

                'nullable',

                'string'

            ]

        ];
    }
}