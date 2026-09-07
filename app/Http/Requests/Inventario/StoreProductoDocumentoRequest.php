<?php

namespace App\Http\Requests\Inventario;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;


class StoreProductoDocumentoRequest
    extends BaseRequest
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


            'tipo' => [

                'required',

                'string',

                Rule::in([

                    'ficha_tecnica',

                    'certificado',

                    'manual',

                    'catalogo',

                    'otro'

                ])

            ],


            'nombre' => [

                'required',

                'string',

                'max:150'

            ],


            'archivo' => [

                'required',

                'string',

                'max:255'

            ],


            'es_publico' => [

                'nullable',

                'boolean'

            ],


            'orden' => [

                'nullable',

                'integer',

                'min:0',

                'max:65535'

            ],


            'observaciones' => [

                'nullable',

                'string'

            ]

        ];

    }

}
