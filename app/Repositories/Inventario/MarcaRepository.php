<?php

namespace App\Repositories\Inventario;

use App\Models\Inventario\Marca;
use Illuminate\Database\Eloquent\Collection;


class MarcaRepository
{

    /*
    |--------------------------------------------------------------------------
    | Listar
    |--------------------------------------------------------------------------
    */

    public function getAll(
        bool $incluirInactivas = false
    ): Collection
    {

        $query =
            Marca::query();


        if (
            !$incluirInactivas
        ) {

            $query->where(
                'estado_registro',
                'A'
            );

        }


        return $query
            ->orderByRaw(
                "CASE
                    WHEN estado_registro = 'A'
                    THEN 0
                    ELSE 1
                END"
            )
            ->orderBy(
                'orden'
            )
            ->orderBy(
                'nombre'
            )
            ->get();

    }


    /*
    |--------------------------------------------------------------------------
    | Buscar
    |--------------------------------------------------------------------------
    */

    public function findById(
        int $id
    ): Marca
    {

        return Marca::findOrFail(
            $id
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Comprobar slug
    |--------------------------------------------------------------------------
    */

    public function slugExists(
        string $slug,
        ?int   $exceptoId = null
    ): bool
    {

        $query =
            Marca::query()
                ->where(
                    'slug',
                    $slug
                );


        if (
            $exceptoId !== null
        ) {

            $query->where(
                'id_marca',
                '<>',
                $exceptoId
            );

        }


        return $query
            ->exists();

    }


    /*
    |--------------------------------------------------------------------------
    | Variantes activas
    |--------------------------------------------------------------------------
    */

    public function hasActiveVariants(
        Marca $marca
    ): bool
    {

        return $marca
            ->variantes()
            ->where(
                'estado_registro',
                'A'
            )
            ->exists();

    }


    /*
    |--------------------------------------------------------------------------
    | Crear
    |--------------------------------------------------------------------------
    */

    public function create(
        array $data
    ): Marca
    {

        return Marca::create(
            $data
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Actualizar
    |--------------------------------------------------------------------------
    */

    public function update(
        Marca $marca,
        array $data
    ): Marca
    {

        $marca->update(
            $data
        );


        return $marca
            ->fresh();

    }


    /*
    |--------------------------------------------------------------------------
    | Desactivar
    |--------------------------------------------------------------------------
    */

    public function delete(
        Marca $marca
    ): bool
    {

        return $marca->update([

            'estado_registro' =>
                'I'

        ]);

    }


    /*
    |--------------------------------------------------------------------------
    | Reactivar
    |--------------------------------------------------------------------------
    */

    public function reactivate(
        Marca $marca
    ): bool
    {

        return $marca->update([

            'estado_registro' =>
                'A'

        ]);

    }

}
