<?php

namespace App\Http\Requests\Inventario;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreStockSucursalRequest extends BaseRequest
{
    /**
     * Reglas para registrar stock por sucursal.
     */
    public function rules(): array
    {
        return [

            'id_sucursal' => [

                'required',

                'integer',

                Rule::exists('sucursal', 'id_sucursal')
                    ->where('estado_registro', 'A')

            ],

            'id_producto_variante' => [

                'required',

                'integer',

                Rule::exists(
                    'producto_variante',
                    'id_producto_variante'
                )
                    ->where('estado_registro', 'A'),

                Rule::unique(
                    'stock_sucursal',
                    'id_producto_variante'
                )
                    ->where(
                        fn ($query) => $query->where(
                            'id_sucursal',
                            $this->input('id_sucursal')
                        )
                    )

            ],

            'stock_actual' => [

                'sometimes',

                'numeric',

                'decimal:0,3',

                'min:0',

                'max:999999999999.999'

            ],

            'stock_minimo' => [

                'sometimes',

                'numeric',

                'decimal:0,3',

                'min:0',

                'max:999999999999.999'

            ],

            'stock_maximo' => [

                'nullable',

                'numeric',

                'decimal:0,3',

                'min:0',

                'max:999999999999.999'

            ],

            'ubicacion_almacen' => [

                'nullable',

                'string',

                'max:150'

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

                $stockMinimo = $this->input(
                    'stock_minimo',
                    0
                );

                $stockMaximo = $this->input(
                    'stock_maximo'
                );

                if (
                    $stockMaximo !== null
                    && is_numeric($stockMinimo)
                    && is_numeric($stockMaximo)
                    && (float) $stockMaximo
                        < (float) $stockMinimo
                ) {

                    $validator->errors()->add(

                        'stock_maximo',

                        'El stock máximo debe ser mayor o igual al stock mínimo.'

                    );

                }

            }

        ];
    }
}