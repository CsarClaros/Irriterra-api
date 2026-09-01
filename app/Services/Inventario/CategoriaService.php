<?php

namespace App\Services\Inventario;

use App\Models\Inventario\Categoria;
use App\Repositories\Inventario\CategoriaRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;


class CategoriaService
{

    public function __construct(
        private readonly CategoriaRepository $repository
    )
    {
    }


    /*
    |--------------------------------------------------------------------------
    | Listar
    |--------------------------------------------------------------------------
    */

    public function index(
        bool $incluirInactivas = false
    ): Collection
    {

        return $this->repository
            ->getAll(
                $incluirInactivas
            );

    }


    /*
    |--------------------------------------------------------------------------
    | Mostrar
    |--------------------------------------------------------------------------
    */

    public function show(
        int $id
    ): Categoria
    {

        return $this->repository
            ->findById(
                $id
            );

    }


    /*
    |--------------------------------------------------------------------------
    | Crear
    |--------------------------------------------------------------------------
    */

    public function store(
        array $data
    ): Categoria
    {

        return DB::transaction(
            function () use (
                $data
            ) {

                $data['estado_registro'] = 'A';


                return $this->repository
                    ->create(
                        $data
                    );

            }
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

        return DB::transaction(
            function () use (
                $categoria,
                $data
            ) {

                return $this->repository
                    ->update(
                        $categoria,
                        $data
                    );

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Desactivar
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Categoria $categoria
    ): bool
    {

        return DB::transaction(
            function () use (
                $categoria
            ) {

                return $this->repository
                    ->delete(
                        $categoria
                    );

            }
        );

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

        return DB::transaction(
            function () use (
                $categoria
            ) {

                return $this->repository
                    ->reactivate(
                        $categoria
                    );

            }
        );

    }

}
