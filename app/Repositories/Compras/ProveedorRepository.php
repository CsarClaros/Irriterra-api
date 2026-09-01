<?php

namespace App\Repositories\Compras;

use App\Models\Compras\Proveedor;
use Illuminate\Database\Eloquent\Collection;


class ProveedorRepository
{

    /*
    |--------------------------------------------------------------------------
    | Listar
    |--------------------------------------------------------------------------
    */

    public function getAll(
        bool $incluirInactivos = false
    ): Collection
    {

        $query =
            Proveedor::query();


        if (
            !$incluirInactivos
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
                'nombre_razon_social'
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
    ): Proveedor
    {

        return Proveedor::findOrFail(
            $id
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Buscar y bloquear
    |--------------------------------------------------------------------------
    */

    public function findByIdForUpdate(
        int $id
    ): Proveedor
    {

        return Proveedor::query()
            ->lockForUpdate()
            ->findOrFail(
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
    ): Proveedor
    {

        return Proveedor::create(
            $data
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Actualizar
    |--------------------------------------------------------------------------
    */

    public function update(
        Proveedor $proveedor,
        array     $data
    ): Proveedor
    {

        $proveedor->update(
            $data
        );


        return $proveedor
            ->fresh();

    }


    /*
    |--------------------------------------------------------------------------
    | Desactivar
    |--------------------------------------------------------------------------
    */

    public function delete(
        Proveedor $proveedor
    ): bool
    {

        return $proveedor->update([

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
        Proveedor $proveedor
    ): bool
    {

        return $proveedor->update([

            'estado_registro' =>
                'A'

        ]);

    }

}
