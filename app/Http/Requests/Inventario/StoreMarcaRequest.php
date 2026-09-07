<?php

namespace App\Http\Requests\Inventario;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;


class StoreMarcaRequest extends BaseRequest
{

    public function rules(): array
    {

        return [

            'nombre' => [

                'required',

                'string',

                'max:150',

                Rule::unique(
                    'marca',
                    'nombre'
                )

            ],


            'pais' => [

                'nullable',

                'string',

                'max:100'

            ],


            'logo' => [

                'nullable',

                'string',

                'max:255'

            ],


            'sitio_web' => [

                'nullable',

                'url',

                'max:255'

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
