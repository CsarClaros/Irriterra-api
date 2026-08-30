<?php

namespace App\Http\Requests\Ventas;

use App\Http\Requests\BaseRequest;
use App\Models\Ventas\Venta;
use Illuminate\Validation\Rule;

class CompletarVentaRequest extends BaseRequest
{
    /**
     * Reglas para completar una venta.
     */
    public function rules(): array
    {
        return [

            'monto_pagado' => [

                'required',

                'numeric',

                'decimal:0,2',

                'gt:0',

                'max:9999999999999.99'

            ],

            'metodo_pago' => [

                'required',

                'string',

                Rule::in(
                    Venta::METODOS_PAGO
                )

            ],

            /*
             * Como no tenemos un campo específico
             * id_usuario_confirmacion, utilizamos
             * usuario_modificacion como auditoría.
             */
            'usuario_modificacion' => [

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