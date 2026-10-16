<?php

namespace App\Http\Requests\Inventario;

use App\Http\Requests\BaseRequest;
use App\Models\Inventario\ProductoVariante;
use Illuminate\Validation\Rule;

class StoreProductoVarianteRequest extends BaseRequest
{
    public function rules(): array
    {
        return [

            'id_producto' => [

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

                'required',

                'string',

                'max:150'

            ],

            /*
             * Temporalmente aceptamos que el frontend
             * todavía envíe SKU, pero lo excluimos.
             *
             * Laravel será quien lo genere.
             */
            'sku' => [

                'exclude'

            ],

            'codigo_comercial' => [

                'nullable',

                'string',

                'max:100'

            ],

            'unidad_medida' => [

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

                'nullable',

                'string'

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
}
