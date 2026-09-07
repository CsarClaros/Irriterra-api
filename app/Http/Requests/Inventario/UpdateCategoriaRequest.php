<?php

namespace App\Http\Requests\Inventario;

use App\Http\Requests\BaseRequest;
use App\Models\Inventario\Categoria;
use Illuminate\Validation\Rule;

class UpdateCategoriaRequest extends BaseRequest
{
    /**
     * Reglas para actualizar
     * una categoría.
     */
    public function rules(): array
    {
        $categoria =
            $this->route(
                'categoria'
            );


        $idCategoria =
            $categoria
            instanceof Categoria

                ? $categoria
                ->id_categoria

                : (
                $categoria
                ??
                $this->route(
                    'id'
                )
            );


        /*
        |--------------------------------------------------------------------------
        | Categoría actual
        |--------------------------------------------------------------------------
        */

        $categoriaActual =
            $categoria
            instanceof Categoria

                ? $categoria

                : Categoria::find(
                $idCategoria
            );


        /*
        |--------------------------------------------------------------------------
        | Padre
        |--------------------------------------------------------------------------
        */

        $idPadre =
            $this->has(
                'id_categoria_padre'
            )

                ? $this->input(
                'id_categoria_padre'
            )

                : $categoriaActual
                ?->id_categoria_padre;


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
                ->ignore(
                    $idCategoria,
                    'id_categoria'
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


        /*
        |--------------------------------------------------------------------------
        | Reglas del padre
        |--------------------------------------------------------------------------
        */

        $reglasPadre = [

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

        ];


        /*
         * Una categoría no puede
         * ser padre de sí misma.
         */
        if (
            $idCategoria
        ) {

            $reglasPadre[] =
                Rule::notIn([
                    (int)
                    $idCategoria
                ]);

        }


        return [

            'id_categoria_padre' =>
                $reglasPadre,


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
