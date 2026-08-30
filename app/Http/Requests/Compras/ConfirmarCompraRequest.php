<?php

namespace App\Http\Requests\Compras;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class ConfirmarCompraRequest extends BaseRequest
{
    /**
     * Reglas para confirmar una compra.
     */
    public function rules(): array
    {
        return [

            'id_usuario_confirmacion' => [

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