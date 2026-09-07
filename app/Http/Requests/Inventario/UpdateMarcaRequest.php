<?php

namespace App\Http\Requests\Inventario;

use App\Http\Requests\BaseRequest;
use App\Models\Inventario\Marca;
use Illuminate\Validation\Rule;


class UpdateMarcaRequest extends BaseRequest
{

    public function rules(): array
    {

        $marca =
            $this->route(
                'marca'
            );


        $idMarca =
            $marca instanceof Marca

                ? $marca
                ->id_marca

                : (
                $marca
                ??
                $this->route(
                    'id'
                )
            );


        return [

            'nombre' => [

                'required',

                'string',

                'max:150',

                Rule::unique(
                    'marca',
                    'nombre'
                )
                    ->ignore(
                        $idMarca,
                        'id_marca'
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
