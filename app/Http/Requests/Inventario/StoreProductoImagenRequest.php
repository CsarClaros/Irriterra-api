<?php

namespace App\Http\Requests\Inventario;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class StoreProductoImagenRequest extends BaseRequest
{
    /**
     * Reglas para registrar una imagen.
     */
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

            'imagen' => [

                'required',

                'file',

                'image',

                'mimes:jpg,jpeg,png,webp',

                'max:5120'

            ],

            'texto_alternativo' => [

                'nullable',

                'string',

                'max:255'

            ],

            'orden' => [

                'sometimes',

                'integer',

                'min:1'

            ],

            'es_principal' => [

                'sometimes',

                'boolean'

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


    public function messages(): array
    {
        return [

            'imagen.required' =>
                'Debe seleccionar una imagen.',

            'imagen.image' =>
                'El archivo seleccionado debe ser una imagen.',

            'imagen.mimes' =>
                'La imagen debe estar en formato JPG, JPEG, PNG o WEBP.',

            'imagen.max' =>
                'La imagen no puede superar los 5 MB.'

        ];
    }
}
