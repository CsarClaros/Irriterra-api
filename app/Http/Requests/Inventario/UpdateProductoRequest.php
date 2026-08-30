<?php

namespace App\Http\Requests\Inventario;

use App\Http\Requests\BaseRequest;
use App\Models\Inventario\Producto;
use Illuminate\Validation\Rule;

class UpdateProductoRequest extends BaseRequest
{
    /**
     * Reglas para actualizar un producto.
     */
    public function rules(): array
    {
        $producto = $this->route('producto');

        $idProducto = $producto instanceof Producto
            ? $producto->id_producto
            : ($producto ?? $this->route('id'));

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
                    ->ignore($idProducto, 'id_producto')

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