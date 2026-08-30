<?php

namespace App\Http\Requests\Compras;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class StoreCompraRequest extends BaseRequest
{
    /**
     * Normaliza los campos opcionales.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([

            'numero_factura' =>
                $this->numero_factura === ''
                    ? null
                    : $this->numero_factura

        ]);
    }

    /**
     * Reglas para registrar una compra.
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

            'id_proveedor' => [

                'required',

                'integer',

                Rule::exists(
                    'proveedor',
                    'id_proveedor'
                )
                    ->where(
                        'estado_registro',
                        'A'
                    )

            ],

            'id_usuario_comprador' => [

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

            'fecha_compra' => [

                'sometimes',

                'date',

                'before_or_equal:now'

            ],

            'numero_factura' => [

                'nullable',

                'string',

                'max:50',

                Rule::unique(
                    'compra',
                    'numero_factura'
                )
                    ->where(

                        fn ($query) =>
                            $query->where(

                                'id_proveedor',

                                $this->input(
                                    'id_proveedor'
                                )

                            )

                    )

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

            'detalles.*.costo_unitario' => [

                'required',

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

            'codigo_compra' => [

                'prohibited'

            ],

            'estado_compra' => [

                'prohibited'

            ],

            'fecha_confirmacion' => [

                'prohibited'

            ],

            'fecha_recepcion' => [

                'prohibited'

            ],

            'fecha_anulacion' => [

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

            'id_usuario_confirmacion' => [

                'prohibited'

            ],

            'id_usuario_recepcion' => [

                'prohibited'

            ],

            'id_usuario_anulacion' => [

                'prohibited'

            ],

            'motivo_anulacion' => [

                'prohibited'

            ],

            'estado_registro' => [

                'prohibited'

            ],

            'usuario_modificacion' => [

                'prohibited'

            ],

            'detalles.*.subtotal' => [

                'prohibited'

            ]

        ];
    }
}