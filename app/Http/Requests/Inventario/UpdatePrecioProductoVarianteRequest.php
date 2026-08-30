<?php

namespace App\Http\Requests\Inventario;

use App\Http\Requests\BaseRequest;
use App\Models\Inventario\PrecioProductoVariante;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdatePrecioProductoVarianteRequest extends BaseRequest
{
    /**
     * Reglas para actualizar precios.
     */
    public function rules(): array
    {
        $precioProductoVariante =
            $this->route(
                'precioProductoVariante'
            );

        if (
            ! $precioProductoVariante
                instanceof PrecioProductoVariante
        ) {

            $precioProductoVariante =
                PrecioProductoVariante::findOrFail(

                    $precioProductoVariante

                );

        }

        return [

            'id_producto_variante' => [

                'sometimes',

                'required',

                'integer',

                Rule::exists(
                    'producto_variante',
                    'id_producto_variante'
                )
                    ->where(
                        'estado_registro',
                        'A'
                    ),

                Rule::unique(
                    'precio_producto_variante',
                    'id_producto_variante'
                )
                    ->ignore(

                        $precioProductoVariante
                            ->id_precio_producto_variante,

                        'id_precio_producto_variante'

                    )

            ],

            'costo_compra' => [

                'sometimes',

                'required',

                'numeric',

                'decimal:0,2',

                'min:0',

                'max:9999999999999.99'

            ],

            'precio_minimo' => [

                'sometimes',

                'required',

                'numeric',

                'decimal:0,2',

                'min:0',

                'max:9999999999999.99'

            ],

            'precio_venta' => [

                'sometimes',

                'required',

                'numeric',

                'decimal:0,2',

                'min:0',

                'max:9999999999999.99'

            ],

            'fecha_vigencia' => [

                'sometimes',

                'required',

                'date'

            ],

            'observaciones' => [

                'sometimes',

                'nullable',

                'string'

            ],

            'usuario_modificacion' => [

                'sometimes',

                'nullable',

                'integer'

            ]

        ];
    }

    /**
     * Validaciones adicionales.
     */
    public function after(): array
    {
        return [

            function (Validator $validator): void {

                $precioProductoVariante =
                    $this->route(
                        'precioProductoVariante'
                    );

                if (
                    ! $precioProductoVariante
                        instanceof PrecioProductoVariante
                ) {

                    $precioProductoVariante =
                        PrecioProductoVariante::findOrFail(

                            $precioProductoVariante

                        );

                }

                $precioMinimo = $this->input(

                    'precio_minimo',

                    $precioProductoVariante
                        ->precio_minimo

                );

                $precioVenta = $this->input(

                    'precio_venta',

                    $precioProductoVariante
                        ->precio_venta

                );

                if (
                    is_numeric($precioMinimo)
                    && is_numeric($precioVenta)
                    && (float) $precioVenta
                        < (float) $precioMinimo
                ) {

                    $validator->errors()->add(

                        'precio_venta',

                        'El precio de venta debe ser mayor o igual al precio mínimo.'

                    );

                }

            }

        ];
    }
}