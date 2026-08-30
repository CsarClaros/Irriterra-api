<?php

namespace App\Http\Requests\Inventario;

use App\Http\Requests\BaseRequest;
use App\Models\Inventario\ProductoVariante;
use Illuminate\Validation\Rule;

class UpdateProductoVarianteRequest extends BaseRequest
{
    /**
     * Reglas para actualizar una variante.
     */
    public function rules(): array
    {
        $productoVariante = $this->route('productoVariante');

        $idProductoVariante =
            $productoVariante instanceof ProductoVariante
                ? $productoVariante->id_producto_variante
                : $productoVariante;

        return [

            'id_producto' => [

                'sometimes',

                'required',

                'integer',

                Rule::exists('producto', 'id_producto')
                    ->where('estado_registro', 'A')

            ],

            'nombre' => [

                'sometimes',

                'required',

                'string',

                'max:150'

            ],

            'sku' => [

                'sometimes',

                'required',

                'string',

                'max:100',

                Rule::unique('producto_variante', 'sku')
                    ->ignore(
                        $idProductoVariante,
                        'id_producto_variante'
                    )

            ],

            'codigo_comercial' => [

                'sometimes',

                'nullable',

                'string',

                'max:100',

                Rule::unique(
                    'producto_variante',
                    'codigo_comercial'
                )
                    ->ignore(
                        $idProductoVariante,
                        'id_producto_variante'
                    )

            ],

            'unidad_medida' => [

                'sometimes',

                'required',

                'string',

                'max:20'

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