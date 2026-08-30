<?php

namespace App\Http\Requests\Compras;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class RecibirCompraRequest extends BaseRequest
{
    /**
     * Reglas para recibir una compra.
     */
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