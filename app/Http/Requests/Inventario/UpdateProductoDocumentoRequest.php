<?php

namespace App\Http\Requests\Inventario;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;


class UpdateProductoDocumentoRequest
    extends BaseRequest
{

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


            'tipo' => [

                'sometimes',

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

                'sometimes',

                'required',

                'string',

                'max:150'

            ],


            'archivo' => [

                'sometimes',

                'required',

                'string',

                'max:255'

            ],


            'es_publico' => [

                'sometimes',

                'boolean'

            ],


            'orden' => [

                'sometimes',

                'integer',

                'min:0',

                'max:65535'

            ],


            'observaciones' => [

                'sometimes',

                'nullable',

                'string'

            ]

        ];

    }

}
