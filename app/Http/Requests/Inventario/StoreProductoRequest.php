<?php

namespace App\Http\Requests\Inventario;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class StoreProductoRequest extends BaseRequest
{
    /**
     * Reglas para registrar un producto.
     */
    public function rules(): array
    {
        return [

            'id_categoria' => [

                'required',

                'integer',

                Rule::exists('categoria', 'id_categoria')
                    ->where('estado_registro', 'A')

            ],

            'nombre' => [

                'required',

                'string',

                'max:150'

            ],

            'marca' => [

                'nullable',

                'string',

                'max:100'

            ],

            'modelo' => [

                'nullable',

                'string',

                'max:100',

                Rule::unique('producto', 'modelo')
                    ->where('estado_registro', 'A')

            ],

            'descripcion' => [

                'nullable',

                'string'

            ],

            'catalogo_pdf' => [

                'nullable',

                'string',

                'max:255'

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