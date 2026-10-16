<?php

namespace App\Http\Requests\Inventario;

use App\Http\Requests\BaseRequest;
use App\Models\Inventario\ProductoVariante;
use Illuminate\Validation\Rule;

class UpdateProductoVarianteRequest extends BaseRequest
{
    public function rules(): array
    {
        return [

            'id_producto' => [

                'sometimes',

                'required',

                'integer',

                Rule::exists(
                    'producto',
                    'id_producto'
                )
                    ->where(
                        'estado_registro',
                        'A'
                    )

            ],

            'id_marca' => [

                'sometimes',

                'nullable',

                'integer',

                Rule::exists(
                    'marca',
                    'id_marca'
                )
                    ->where(
                        'estado_registro',
                        'A'
                    )

            ],

            'nombre' => [

                'sometimes',

                'required',

                'string',

                'max:150'

            ],

            /*
             * El SKU es inmutable.
             *
             * Por compatibilidad con el frontend
             * actual simplemente se excluye.
             */
            'sku' => [

                'exclude'

            ],

            'codigo_comercial' => [

                'sometimes',

                'nullable',

                'string',

                'max:100'

            ],

            'unidad_medida' => [

                'sometimes',

                'required',

                'string',

                'max:20'

            ],

            'tipo_control_inventario' => [

                'sometimes',

                'required',

                'string',

                Rule::in(
                    ProductoVariante::
                    TIPOS_CONTROL_INVENTARIO
                )

            ],

            'descripcion' => [

                'sometimes',

                'nullable',

                'string'

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
}
