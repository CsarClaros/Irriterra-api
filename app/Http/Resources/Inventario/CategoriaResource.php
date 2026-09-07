<?php

namespace App\Http\Resources\Inventario;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class CategoriaResource
    extends JsonResource
{

    /**
     * Convierte el recurso
     * en un arreglo.
     */
    public function toArray(
        Request $request
    ): array
    {

        return [

            /*
            |--------------------------------------------------------------------------
            | Identificación
            |--------------------------------------------------------------------------
            */

            'id_categoria' =>
                $this->id_categoria,

            'id_categoria_padre' =>
                $this->id_categoria_padre,


            /*
            |--------------------------------------------------------------------------
            | Información
            |--------------------------------------------------------------------------
            */

            'nombre' =>
                $this->nombre,

            'slug' =>
                $this->slug,

            'descripcion' =>
                $this->descripcion,

            'orden' =>
                $this->orden,

            'observaciones' =>
                $this->observaciones,


            /*
            |--------------------------------------------------------------------------
            | Jerarquía
            |--------------------------------------------------------------------------
            */

            'es_raiz' =>
                $this->id_categoria_padre
                === null,


            'padre' =>
                $this->whenLoaded(
                    'padre',
                    function () {

                        if (
                            !$this->padre
                        ) {

                            return null;

                        }


                        return [

                            'id_categoria' =>
                                $this->padre
                                    ->id_categoria,

                            'nombre' =>
                                $this->padre
                                    ->nombre,

                            'slug' =>
                                $this->padre
                                    ->slug,

                            'estado_registro' =>
                                $this->padre
                                    ->estado_registro

                        ];

                    }
                ),


            /*
            |--------------------------------------------------------------------------
            | Estado
            |--------------------------------------------------------------------------
            */

            'estado_registro' =>
                $this->estado_registro,


            /*
            |--------------------------------------------------------------------------
            | Auditoría
            |--------------------------------------------------------------------------
            */

            'created_at' =>
                $this->created_at,

            'updated_at' =>
                $this->updated_at

        ];

    }

}
