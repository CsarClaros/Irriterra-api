<?php

namespace App\Repositories\Inventario;

use App\Models\Inventario\Categoria;
use Illuminate\Database\Eloquent\Collection;


class CategoriaRepository
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
            Categoria::query();


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
                'nombre'
            )
            ->get();

    }


    /*
    |--------------------------------------------------------------------------
    | Buscar por ID
    |--------------------------------------------------------------------------
    */

    public function findById(
        int $id
    ): Categoria
    {

        return Categoria::findOrFail(
            $id
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Crear
    |--------------------------------------------------------------------------
    */

    public function create(
        array $data
    ): Categoria
    {

        return Categoria::create(
            $data
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Actualizar
    |--------------------------------------------------------------------------
    */

    public function update(
        Categoria $categoria,
        array     $data
    ): Categoria
    {

        $categoria->update(
            $data
        );


        return $categoria
            ->fresh();

    }


    /*
    |--------------------------------------------------------------------------
    | Desactivar
    |--------------------------------------------------------------------------
    */

    public function delete(
        Categoria $categoria
    ): bool
    {

        return $categoria->update([

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
        Categoria $categoria
    ): bool
    {

        return $categoria->update([

            'estado_registro' =>
                'A'

        ]);

    }

}
