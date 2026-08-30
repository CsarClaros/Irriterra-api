<?php

namespace App\Http\Requests\Inventario;

use App\Http\Requests\BaseRequest;
use App\Models\Inventario\MovimientoInventario;
use Illuminate\Validation\Rule;

class StoreMovimientoInventarioRequest extends BaseRequest
{
    /**
     * Reglas para registrar un movimiento.
     */
    public function rules(): array
    {
        return [

            'id_stock_sucursal' => [

                'required',

                'integer',

                Rule::exists(
                    'stock_sucursal',
                    'id_stock_sucursal'
                )
                    ->where('estado_registro', 'A')

            ],

            'tipo_movimiento' => [

                'required',

                'string',

                Rule::in(
                    MovimientoInventario::TIPOS
                )

            ],

            'cantidad' => [

                'required',

                'numeric',

                'decimal:0,3',

                'gt:0',

                'max:999999999999.999'

            ],

            'motivo' => [

                'required',

                'string',

                'max:150'

            ],

            'tipo_referencia' => [

                'nullable',

                'required_with:id_referencia',

                'string',

                'max:50'

            ],

            'id_referencia' => [

                'nullable',

                'required_with:tipo_referencia',

                'integer',

                'min:1'

            ],

            'fecha_movimiento' => [

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

                'integer'

            ]

        ];
    }
}