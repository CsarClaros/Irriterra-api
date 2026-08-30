<?php

namespace App\Http\Requests\Inventario;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class StoreProductoVarianteRequest extends BaseRequest
{
    /**
     * Reglas para registrar una variante.
     */
    public function rules(): array
    {
        return [

            'id_producto' => [

                'required',

                'integer',

                Rule::exists('producto', 'id_producto')
                    ->where('estado_registro', 'A')

            ],

            'nombre' => [

                'required',

                'string',

                'max:150'

            ],

            'sku' => [

                'required',

                'string',

                'max:100',

                Rule::unique('producto_variante', 'sku')

            ],

            'codigo_comercial' => [

                'nullable',

                'string',

                'max:100',

                Rule::unique(
                    'producto_variante',
                    'codigo_comercial'
                )

            ],

            'unidad_medida' => [

                'required',

                'string',

                'max:20'

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