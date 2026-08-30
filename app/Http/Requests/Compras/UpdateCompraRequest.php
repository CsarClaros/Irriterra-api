<?php

namespace App\Http\Requests\Compras;

use App\Http\Requests\BaseRequest;
use App\Models\Compras\Compra;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateCompraRequest extends BaseRequest
{
    /**
     * Normaliza los campos opcionales.
     */
    protected function prepareForValidation(): void
    {
        if (
            $this->exists(
                'numero_factura'
            )
        ) {

            $this->merge([

                'numero_factura' =>
                    $this->numero_factura === ''
                        ? null
                        : $this->numero_factura

            ]);

        }
    }

    /**
     * Reglas para actualizar una compra.
     */
    public function rules(): array
    {
        $compra =
            $this->route(
                'compra'
            );

        if (
            ! $compra instanceof Compra
        ) {

            $compra =
                Compra::findOrFail(
                    $compra
                );

        }

        $idProveedor =
            $this->input(

                'id_proveedor',

                $compra->id_proveedor

            );

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

            'id_proveedor' => [

                'sometimes',

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

            'fecha_compra' => [

                'sometimes',

                'required',

                'date',

                'before_or_equal:now'

            ],

            'numero_factura' => [

                'sometimes',

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

                                $idProveedor

                            )

                    )
                    ->ignore(

                        $compra->id_compra,

                        'id_compra'

                    )

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

            'detalles.*.costo_unitario' => [

                'required_with:detalles',

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

            'usuario_creacion' => [

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

                $compra =
                    $this->route(
                        'compra'
                    );

                if (
                    ! $compra instanceof Compra
                ) {

                    $compra =
                        Compra::findOrFail(
                            $compra
                        );

                }

                if (
                    $compra->estado_compra
                    !== Compra::BORRADOR
                ) {

                    $validator->errors()->add(

                        'estado_compra',

                        'Solo pueden actualizarse compras en borrador.'

                    );

                }

            }

        ];
    }
}