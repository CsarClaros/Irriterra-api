<?php

namespace App\Http\Requests\Ventas;

use App\Http\Requests\BaseRequest;
use App\Models\Ventas\Venta;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateVentaRequest extends BaseRequest
{
    /**
     * Reglas para actualizar una venta.
     */
    public function rules(): array
    {
        return [

            'id_sucursal' => [

                'sometimes',

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

                'sometimes',

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

                'sometimes',

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

                'required',

                'date',

                'before_or_equal:now'

            ],

            'observaciones' => [

                'sometimes',

                'nullable',

                'string'

            ],

            'usuario_modificacion' => [

                'sometimes',

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

                'sometimes',

                'required',

                'array',

                'min:1'

            ],

            'detalles.*.id_producto_variante' => [

                'required_with:detalles',

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

                'required_with:detalles',

                'numeric',

                'decimal:0,3',

                'gt:0',

                'max:999999999999.999'

            ],

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
            | Campos protegidos
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

            'usuario_creacion' => [

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

    /**
     * Validaciones posteriores.
     */
    public function after(): array
    {
        return [

            function (
                Validator $validator
            ): void {

                $venta =
                    $this->route(
                        'venta'
                    );

                if (
                    ! $venta instanceof Venta
                ) {

                    $venta =
                        Venta::findOrFail(
                            $venta
                        );

                }

                if (
                    $venta->estado_venta
                    !== Venta::BORRADOR
                ) {

                    $validator->errors()->add(

                        'estado_venta',

                        'Solo pueden actualizarse ventas en borrador.'

                    );

                }

            }

        ];
    }
}