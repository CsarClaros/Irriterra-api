<?php

namespace App\Repositories\Ventas;

use App\Models\Ventas\Cliente;
use Illuminate\Database\Eloquent\Collection;


class ClienteRepository
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
            Cliente::query();


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
    ): Cliente
    {

        return Cliente::findOrFail(
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
    ): Cliente
    {

        return Cliente::query()
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
    ): Cliente
    {

        return Cliente::create(
            $data
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Actualizar
    |--------------------------------------------------------------------------
    */

    public function update(
        Cliente $cliente,
        array   $data
    ): Cliente
    {

        $cliente->update(
            $data
        );


        return $cliente
            ->fresh();

    }


    /*
    |--------------------------------------------------------------------------
    | Desactivar
    |--------------------------------------------------------------------------
    */

    public function delete(
        Cliente $cliente
    ): bool
    {

        return $cliente->update([

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
        Cliente $cliente
    ): bool
    {

        return $cliente->update([

            'estado_registro' =>
                'A'

        ]);

    }

}
