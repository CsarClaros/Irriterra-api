<?php

namespace App\Services\Inventario;

use App\Models\Inventario\Marca;
use App\Repositories\Inventario\MarcaRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;


class MarcaService
{

    public function __construct(
        private readonly MarcaRepository $repository
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
    ): Marca
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
    ): Marca
    {

        return DB::transaction(
            function () use (
                $data
            ) {

                $data['orden'] =
                    (int)
                    (
                        $data['orden']
                        ?? 0
                    );


                $data['slug'] =
                    $this->generarSlug(
                        $data['nombre']
                    );


                $data['estado_registro'] =
                    'A';


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
        Marca $marca,
        array $data
    ): Marca
    {

        return DB::transaction(
            function () use (
                $marca,
                $data
            ) {

                $nombre =
                    $data['nombre']
                    ?? $marca->nombre;


                $data['slug'] =
                    $this->generarSlug(

                        $nombre,

                        $marca
                            ->id_marca

                    );


                $data['orden'] =
                    array_key_exists(
                        'orden',
                        $data
                    )

                        ? (int)
                    (
                        $data['orden']
                        ?? 0
                    )

                        : $marca
                        ->orden;


                return $this->repository
                    ->update(

                        $marca,

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
        Marca $marca
    ): bool
    {

        return DB::transaction(
            function () use (
                $marca
            ) {

                if (
                    $this->repository
                        ->hasActiveVariants(
                            $marca
                        )
                ) {

                    throw ValidationException::withMessages([

                        'marca' =>
                            'No se puede desactivar la marca porque tiene variantes de producto activas asociadas.'

                    ]);

                }


                return $this->repository
                    ->delete(
                        $marca
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
        Marca $marca
    ): bool
    {

        return DB::transaction(
            function () use (
                $marca
            ) {

                return $this->repository
                    ->reactivate(
                        $marca
                    );

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Generar slug
    |--------------------------------------------------------------------------
    */

    private function generarSlug(
        string $nombre,
        ?int   $exceptoId = null
    ): string
    {

        $base =
            Str::slug(
                $nombre
            );


        if (
            $base === ''
        ) {

            $base =
                'marca';

        }


        $slug =
            $base;


        $numero =
            2;


        while (
        $this->repository
            ->slugExists(

                $slug,

                $exceptoId

            )
        ) {

            $slug =
                $base
                . '-'
                . $numero;


            $numero++;

        }


        return $slug;

    }

}
