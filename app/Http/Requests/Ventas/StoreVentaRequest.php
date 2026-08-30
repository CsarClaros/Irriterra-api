<?php

namespace App\Http\Requests\Ventas;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class StoreVentaRequest extends BaseRequest
{
    /**
     * Reglas para registrar una venta.
     */
    public function rules(): array
    {
        return [

            'id_sucursal' => [

                'required',

                'integer',

                Rule::exists(
                    'sucursal',
                    'id_sucursal'
                )
                    ->where(
                        'estado_registro',
                        'A'
                    )

            ],

            'id_cliente' => [

                'nullable',

                'integer',

                Rule::exists(
                    'cliente',
                    'id_cliente'
                )
                    ->where(
                        'estado_registro',
                        'A'
                    )

            ],

            'id_usuario_vendedor' => [

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

            'fecha_venta' => [

                'sometimes',

                'date',

                'before_or_equal:now'

            ],

            'observaciones' => [

                'nullable',

                'string'

            ],

            'usuario_creacion' => [

                'nullable',

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

            /*
            |--------------------------------------------------------------------------
            | Detalles
            |--------------------------------------------------------------------------
            */

            'detalles' => [

                'required',

                'array',

                'min:1'

            ],

            'detalles.*.id_producto_variante' => [

                'required',

                'integer',

                'distinct',

                Rule::exists(
                    'producto_variante',
                    'id_producto_variante'
                )
                    ->where(
                        'estado_registro',
                        'A'
                    )

            ],

            'detalles.*.cantidad' => [

                'required',

                'numeric',

                'decimal:0,3',

                'gt:0',

                'max:999999999999.999'

            ],

            /*
             * Si no se envía precio_unitario,
             * se usa precio_venta.
             */
            'detalles.*.precio_unitario' => [

                'sometimes',

                'nullable',

                'numeric',

                'decimal:0,2',

                'gt:0',

                'max:9999999999999.99'

            ],

            'detalles.*.descuento' => [

                'sometimes',

                'numeric',

                'decimal:0,2',

                'min:0',

                'max:9999999999999.99'

            ],

            'detalles.*.observaciones' => [

                'nullable',

                'string'

            ],

            /*
            |--------------------------------------------------------------------------
            | Campos controlados por el servidor
            |--------------------------------------------------------------------------
            */

            'codigo_venta' => [

                'prohibited'

            ],

            'estado_venta' => [

                'prohibited'

            ],

            'subtotal' => [

                'prohibited'

            ],

            'descuento' => [

                'prohibited'

            ],

            'total' => [

                'prohibited'

            ],

            'monto_pagado' => [

                'prohibited'

            ],

            'metodo_pago' => [

                'prohibited'

            ],

            'fecha_pago' => [

                'prohibited'

            ],

            'fecha_anulacion' => [

                'prohibited'

            ],

            'motivo_anulacion' => [

                'prohibited'

            ],

            'id_usuario_anulacion' => [

                'prohibited'

            ],

            'estado_registro' => [

                'prohibited'

            ],

            'usuario_modificacion' => [

                'prohibited'

            ],

            'detalles.*.costo_unitario' => [

                'prohibited'

            ],

            'detalles.*.subtotal' => [

                'prohibited'

            ]

        ];
    }
}