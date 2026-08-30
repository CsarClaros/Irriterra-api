<?php

namespace App\Http\Requests\Reportes;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class ReporteStockInventarioRequest extends BaseRequest
{
    /**
     * Reglas para consultar reportes de stock.
     */
    public function rules(): array
    {
        return [

            'id_sucursal' => [

                'sometimes',

                'nullable',

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

            'id_producto_variante' => [

                'sometimes',

                'nullable',

                'integer',

                Rule::exists(
                    'producto_variante',
                    'id_producto_variante'
                )
                    ->where(
                        'estado_registro',
                        'A'
                    )

            ],

            'id_categoria' => [

                'sometimes',

                'nullable',

                'integer',

                Rule::exists(
                    'categoria',
                    'id_categoria'
                )
                    ->where(
                        'estado_registro',
                        'A'
                    )

            ],

            'estado_stock' => [

                'sometimes',

                'nullable',

                'string',

                Rule::in([

                    'TODOS',

                    'AGOTADO',

                    'BAJO',

                    'NORMAL'

                ])

            ]

        ];
    }
}