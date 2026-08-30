<?php

namespace App\Http\Requests\Inventario;

use App\Http\Requests\BaseRequest;
use App\Models\Inventario\ProductoImagen;
use Illuminate\Validation\Rule;

class UpdateProductoImagenRequest extends BaseRequest
{
    /**
     * Reglas para actualizar una imagen.
     */
    public function rules(): array
    {
        $productoImagen = $this->route('productoImagen');

        $idProductoImagen =
            $productoImagen instanceof ProductoImagen
                ? $productoImagen->id_producto_imagen
                : $productoImagen;

        $idProducto =
            $this->input('id_producto')
            ?? (
                $productoImagen instanceof ProductoImagen
                    ? $productoImagen->id_producto
                    : null
            );

        return [

            'id_producto' => [

                'sometimes',

                'required',

                'integer',

                Rule::exists('producto', 'id_producto')
                    ->where('estado_registro', 'A')

            ],

            'ruta_imagen' => [

                'sometimes',

                'required',

                'string',

                'max:255',

                Rule::unique(
                    'producto_imagen',
                    'ruta_imagen'
                )
                    ->where(
                        fn ($query) => $query->where(
                            'id_producto',
                            $idProducto
                        )
                    )
                    ->ignore(
                        $idProductoImagen,
                        'id_producto_imagen'
                    )

            ],

            'texto_alternativo' => [

                'sometimes',

                'nullable',

                'string',

                'max:255'

            ],

            'orden' => [

                'sometimes',

                'required',

                'integer',

                'min:1'

            ],

            'es_principal' => [

                'sometimes',

                'required',

                'boolean'

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