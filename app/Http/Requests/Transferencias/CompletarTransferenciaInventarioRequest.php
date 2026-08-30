<?php

namespace App\Http\Requests\Transferencias;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class CompletarTransferenciaInventarioRequest extends BaseRequest
{
    public function rules(): array
    {
        return [

            'id_usuario_recepcion' => [

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

            ]

        ];
    }
}