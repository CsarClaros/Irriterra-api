<?php

namespace App\Http\Requests\Transferencias;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class RechazarTransferenciaInventarioRequest extends BaseRequest
{
    public function rules(): array
    {
        return [

            'id_usuario_rechazo' => [

                'required',

                'integer',

                Rule::exists(
                    'usuario',
                    'id_usuario'
                )
                    ->where(
                        'estado_registro',
                        'A'
                    )

            ],

            'motivo_rechazo' => [

                'required',

                'string',

                'max:255'

            ]

        ];
    }
}