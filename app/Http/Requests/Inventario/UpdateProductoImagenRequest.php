<?php

namespace App\Http\Requests\Inventario;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateProductoImagenRequest extends BaseRequest
{
    /**
     * Reglas para actualizar una imagen.
     */
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

            'id_producto_variante' => [

                'sometimes',

                'nullable',

                'integer',

                Rule::exists(
                    'producto_variante',
                    'id_producto_variante'
                )
                    ->where(
                        function ($query) use (
                            $idProducto
                        ) {

                            $query
                                ->where(
                                    'id_producto',
                                    $idProducto
                                )
                                ->where(
                                    'estado_registro',
                                    'A'
                                );

                        }
                    )

            ],

            'imagen' => [

                'sometimes',

                'nullable',

                'file',

                'image',

                'mimes:jpg,jpeg,png,webp',

                'max:5120'

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


    public function messages(): array
    {
        return [

            'imagen.image' =>
                'El archivo seleccionado debe ser una imagen.',

            'imagen.mimes' =>
                'La imagen debe estar en formato JPG, JPEG, PNG o WEBP.',

            'imagen.max' =>
                'La imagen no puede superar los 5 MB.'

        ];
    }
}
