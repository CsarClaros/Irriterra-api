<?php

namespace App\Http\Requests\Inventario;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class StoreCategoriaRequest extends BaseRequest
{
    /**
     * Reglas de validación.
     */
    public function rules(): array
    {
        $idPadre =
            $this->input(
                'id_categoria_padre'
            );


        if (
            $idPadre === ''
        ) {

            $idPadre =
                null;

        }


        /*
        |--------------------------------------------------------------------------
        | Nombre único dentro del mismo padre
        |--------------------------------------------------------------------------
        */

        $nombreUnico =
            Rule::unique(
                'categoria',
                'nombre'
            )
                ->where(
                    function (
                        $query
                    ) use (
                        $idPadre
                    ) {

                        if (
                            $idPadre === null
                        ) {

                            $query
                                ->whereNull(
                                    'id_categoria_padre'
                                );

                            return;

                        }


                        $query
                            ->where(
                                'id_categoria_padre',
                                $idPadre
                            );

                    }
                );


        return [

            /*
            |--------------------------------------------------------------------------
            | Jerarquía
            |--------------------------------------------------------------------------
            */

            'id_categoria_padre' => [

                'nullable',

                'integer',

                Rule::exists(
                    'categoria',
                    'id_categoria'
                )
                    ->where(
                        fn($query) => $query->where(
                            'estado_registro',
                            'A'
                        )
                    )

            ],


            /*
            |--------------------------------------------------------------------------
            | Información general
            |--------------------------------------------------------------------------
            */

            'nombre' => [

                'required',

                'string',

                'max:150',

                $nombreUnico

            ],


            'descripcion' => [

                'nullable',

                'string',

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
