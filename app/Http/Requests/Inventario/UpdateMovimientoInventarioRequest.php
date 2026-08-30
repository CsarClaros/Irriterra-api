<?php

namespace App\Http\Requests\Inventario;

use App\Http\Requests\BaseRequest;

class UpdateMovimientoInventarioRequest extends BaseRequest
{
    /**
     * Reglas para actualizar información complementaria
     * de un movimiento de inventario.
     */
    public function rules(): array
    {
        return [

            'motivo' => [

                'sometimes',

                'required',

                'string',

                'max:150'

            ],

            'tipo_referencia' => [

                'sometimes',

                'nullable',

                'required_with:id_referencia',

                'string',

                'max:50'

            ],

            'id_referencia' => [

                'sometimes',

                'nullable',

                'required_with:tipo_referencia',

                'integer',

                'min:1'

            ],

            'fecha_movimiento' => [

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

                'integer'

            ],

            'id_stock_sucursal' => [

                'prohibited'

            ],

            'codigo_movimiento' => [

                'prohibited'

            ],

            'tipo_movimiento' => [

                'prohibited'

            ],

            'cantidad' => [

                'prohibited'

            ],

            'stock_anterior' => [

                'prohibited'

            ],

            'stock_resultante' => [

                'prohibited'

            ],

            'estado_registro' => [

                'prohibited'

            ],

        ];
    }
}
