<?php

namespace App\Http\Requests\Inventario;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePrecioProductoVarianteRequest extends BaseRequest
{
    /**
     * Reglas para registrar precios.
     */
    public function rules(): array
    {
        return [

            'id_producto_variante' => [

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

            ],

            'costo_compra' => [

                'required',

                'numeric',

                'decimal:0,2',

                'min:0',

                'max:9999999999999.99'

            ],

            'precio_minimo' => [

                'required',

                'numeric',

                'decimal:0,2',

                'min:0',

                'max:9999999999999.99'

            ],

            'precio_venta' => [

                'required',

                'numeric',

                'decimal:0,2',

                'min:0',

                'max:9999999999999.99'

            ],

            'fecha_vigencia' => [

                'sometimes',

                'date'

            ],

            'observaciones' => [

                'nullable',

                'string'

            ],

            'usuario_creacion' => [

                'nullable',

                'integer'

            ],

            'usuario_modificacion' => [

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

                $precioMinimo =
                    $this->input('precio_minimo');

                $precioVenta =
                    $this->input('precio_venta');

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