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

                Rule::exists('producto', 'id_producto')
                    ->where('estado_registro', 'A')

            ],

            'ruta_imagen' => [

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
                            $this->input('id_producto')
                        )
                    )

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
}