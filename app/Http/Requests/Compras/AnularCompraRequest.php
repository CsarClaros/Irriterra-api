<?php

namespace App\Http\Requests\Compras;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class AnularCompraRequest extends BaseRequest
{
    /**
     * Reglas para anular una compra.
     */
    public function rules(): array
    {
        return [

            'id_usuario_anulacion' => [

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

            'motivo_anulacion' => [

                'required',

                'string',

                'max:255'

            ]

        ];
    }
}