<?php

namespace App\Http\Requests\Inventario;

use App\Http\Requests\BaseRequest;
use App\Models\Inventario\StockSucursal;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateStockSucursalRequest extends BaseRequest
{
    /**
     * Reglas para actualizar stock por sucursal.
     */
    public function rules(): array
    {
        $stockSucursal = $this->route('stockSucursal');

        if (! $stockSucursal instanceof StockSucursal) {

            $stockSucursal = StockSucursal::findOrFail(
                $stockSucursal
            );

        }

        $idSucursal = $this->input(
            'id_sucursal',
            $stockSucursal->id_sucursal
        );

        return [

            'id_sucursal' => [

                'sometimes',

                'required',

                'integer',

                Rule::exists('sucursal', 'id_sucursal')
                    ->where('estado_registro', 'A')

            ],

            'id_producto_variante' => [

                'sometimes',

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
                            $idSucursal
                        )
                    )
                    ->ignore(
                        $stockSucursal->id_stock_sucursal,
                        'id_stock_sucursal'
                    )

            ],

            'stock_actual' => [

                'sometimes',

                'required',

                'numeric',

                'decimal:0,3',

                'min:0',

                'max:999999999999.999'

            ],

            'stock_minimo' => [

                'sometimes',

                'required',

                'numeric',

                'decimal:0,3',

                'min:0',

                'max:999999999999.999'

            ],

            'stock_maximo' => [

                'sometimes',

                'nullable',

                'numeric',

                'decimal:0,3',

                'min:0',

                'max:999999999999.999'

            ],

            'ubicacion_almacen' => [

                'sometimes',

                'nullable',

                'string',

                'max:150'

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

                $stockSucursal = $this->route(
                    'stockSucursal'
                );

                if (! $stockSucursal instanceof StockSucursal) {

                    $stockSucursal = StockSucursal::findOrFail(
                        $stockSucursal
                    );

                }

                $stockMinimo = $this->input(
                    'stock_minimo',
                    $stockSucursal->stock_minimo
                );

                $stockMaximo = $this->exists(
                    'stock_maximo'
                )
                    ? $this->input('stock_maximo')
                    : $stockSucursal->stock_maximo;

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

                $idSucursal = $this->input(
                    'id_sucursal',
                    $stockSucursal->id_sucursal
                );

                $idProductoVariante = $this->input(
                    'id_producto_variante',
                    $stockSucursal->id_producto_variante
                );

                $combinacionDuplicada =
                    StockSucursal::where(
                        'id_sucursal',
                        $idSucursal
                    )
                        ->where(
                            'id_producto_variante',
                            $idProductoVariante
                        )
                        ->where(
                            'id_stock_sucursal',
                            '<>',
                            $stockSucursal->id_stock_sucursal
                        )
                        ->exists();

                if ($combinacionDuplicada) {

                    $validator->errors()->add(

                        'id_producto_variante',

                        'La variante ya posee un registro de stock en esta sucursal.'

                    );

                }

            }

        ];
    }
}