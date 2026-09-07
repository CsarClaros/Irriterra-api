<?php

namespace App\Services\Inventario;

use App\Models\Inventario\Categoria;
use App\Repositories\Inventario\CategoriaRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;


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

                $idPadre =
                    $data['id_categoria_padre']
                    ?? null;


                if (
                    $idPadre !== null
                ) {

                    $idPadre =
                        (int)
                        $idPadre;

                }


                $this->validarPadre(
                    $idPadre
                );


                $data['id_categoria_padre'] =
                    $idPadre;


                $data['orden'] =
                    (int)
                    (
                        $data['orden']
                        ?? 0
                    );


                $data['slug'] =
                    $this->generarSlug(

                        $data['nombre'],

                        $idPadre

                    );


                $data['estado_registro'] =
                    'A';


                $categoria =
                    $this->repository
                        ->create(
                            $data
                        );


                return $this->repository
                    ->findById(
                        $categoria
                            ->id_categoria
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

                /*
                |--------------------------------------------------------------------------
                | Nuevo padre
                |--------------------------------------------------------------------------
                */

                $idPadre =
                    array_key_exists(
                        'id_categoria_padre',
                        $data
                    )

                        ? $data['id_categoria_padre']

                        : $categoria
                        ->id_categoria_padre;


                if (
                    $idPadre !== null
                ) {

                    $idPadre =
                        (int)
                        $idPadre;

                }


                $this->validarPadre(

                    $idPadre,

                    $categoria

                );


                /*
                |--------------------------------------------------------------------------
                | Valores
                |--------------------------------------------------------------------------
                */

                $nombre =
                    $data['nombre']
                    ?? $categoria
                    ->nombre;


                $data['id_categoria_padre'] =
                    $idPadre;


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

                        : $categoria
                        ->orden;


                /*
                |--------------------------------------------------------------------------
                | Slug
                |--------------------------------------------------------------------------
                */

                $slugAnterior =
                    $categoria
                        ->slug;


                $data['slug'] =
                    $this->generarSlug(

                        $nombre,

                        $idPadre,

                        $categoria
                            ->id_categoria

                    );


                /*
                |--------------------------------------------------------------------------
                | Actualización
                |--------------------------------------------------------------------------
                */

                $categoriaActualizada =
                    $this->repository
                        ->update(

                            $categoria,

                            $data

                        );


                /*
                |--------------------------------------------------------------------------
                | Actualizar slugs descendientes
                |--------------------------------------------------------------------------
                |
                | Si cambia:
                |
                | Riego > Tuberías
                |
                | a:
                |
                | Sistemas de Riego > Tuberías
                |
                | también debe cambiar:
                |
                | riego-tuberias-pvc
                |
                | por:
                |
                | sistemas-de-riego-tuberias-pvc
                |
                */

                if (
                    $slugAnterior
                    !==
                    $categoriaActualizada
                        ->slug
                ) {

                    $this
                        ->actualizarSlugsDescendientes(
                            $categoriaActualizada
                        );

                }


                return $this->repository
                    ->findById(
                        $categoriaActualizada
                            ->id_categoria
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

                /*
                |--------------------------------------------------------------------------
                | Hijas activas
                |--------------------------------------------------------------------------
                */

                if (
                    $this->repository
                        ->hasActiveChildren(
                            $categoria
                        )
                ) {

                    throw ValidationException::withMessages([

                        'categoria' =>
                            'No se puede desactivar la categoría porque tiene subcategorías activas.'

                    ]);

                }


                /*
                |--------------------------------------------------------------------------
                | Productos activos
                |--------------------------------------------------------------------------
                */

                if (
                    $this->repository
                        ->hasActiveProducts(
                            $categoria
                        )
                ) {

                    throw ValidationException::withMessages([

                        'categoria' =>
                            'No se puede desactivar la categoría porque tiene productos activos asociados.'

                    ]);

                }


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

                /*
                |--------------------------------------------------------------------------
                | Padre activo
                |--------------------------------------------------------------------------
                */

                if (
                    $categoria
                        ->id_categoria_padre
                    !== null
                ) {

                    $padre =
                        $this->repository
                            ->findById(
                                $categoria
                                    ->id_categoria_padre
                            );


                    if (
                        $padre
                            ->estado_registro
                        !== 'A'
                    ) {

                        throw ValidationException::withMessages([

                            'categoria' =>
                                'No se puede reactivar la categoría mientras su categoría padre esté inactiva.'

                        ]);

                    }

                }


                return $this->repository
                    ->reactivate(
                        $categoria
                    );

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Validar categoría padre
    |--------------------------------------------------------------------------
    */

    private function validarPadre(
        ?int       $idPadre,
        ?Categoria $categoriaActual = null
    ): void
    {

        if (
            $idPadre === null
        ) {

            return;

        }


        $padre =
            $this->repository
                ->findById(
                    $idPadre
                );


        /*
        |--------------------------------------------------------------------------
        | Padre activo
        |--------------------------------------------------------------------------
        */

        if (
            $padre
                ->estado_registro
            !== 'A'
        ) {

            throw ValidationException::withMessages([

                'id_categoria_padre' =>
                    'La categoría padre debe estar activa.'

            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | Creación
        |--------------------------------------------------------------------------
        */

        if (
            $categoriaActual === null
        ) {

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | No puede ser padre de sí misma
        |--------------------------------------------------------------------------
        */

        if (
            $padre
                ->id_categoria
            ===
            $categoriaActual
                ->id_categoria
        ) {

            throw ValidationException::withMessages([

                'id_categoria_padre' =>
                    'Una categoría no puede ser padre de sí misma.'

            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | Evitar ciclos
        |--------------------------------------------------------------------------
        |
        | Ejemplo inválido:
        |
        | Riego
        | └── Tuberías
        |     └── PVC
        |
        | luego intentar:
        |
        | Riego.padre = PVC
        |
        */

        $visitados =
            [];


        $actual =
            $padre;


        while (
            $actual !== null
        ) {

            /*
             * Si encontramos la categoría
             * que estamos modificando,
             * el nuevo padre pertenece
             * a sus descendientes.
             */
            if (
                $actual
                    ->id_categoria
                ===
                $categoriaActual
                    ->id_categoria
            ) {

                throw ValidationException::withMessages([

                    'id_categoria_padre' =>
                        'No se puede asignar una subcategoría como padre porque se produciría un ciclo.'

                ]);

            }


            /*
             * Protección adicional ante
             * datos previamente corruptos.
             */
            if (
                isset(
                    $visitados[$actual
                        ->id_categoria]
                )
            ) {

                throw ValidationException::withMessages([

                    'id_categoria_padre' =>
                        'Se detectó una relación circular entre categorías.'

                ]);

            }


            $visitados[$actual
                ->id_categoria] =
                true;


            if (
                $actual
                    ->id_categoria_padre
                === null
            ) {

                break;

            }


            $actual =
                $this->repository
                    ->findById(
                        $actual
                            ->id_categoria_padre
                    );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Generar slug
    |--------------------------------------------------------------------------
    */

    private function generarSlug(
        string $nombre,
        ?int   $idPadre = null,
        ?int   $exceptoId = null
    ): string
    {

        $segmento =
            Str::slug(
                $nombre
            );


        if (
            $segmento === ''
        ) {

            $segmento =
                'categoria';

        }


        /*
        |--------------------------------------------------------------------------
        | Slug jerárquico
        |--------------------------------------------------------------------------
        */

        if (
            $idPadre !== null
        ) {

            $padre =
                $this->repository
                    ->findById(
                        $idPadre
                    );


            $base =
                $padre
                    ->slug
                . '-'
                . $segmento;

        } else {

            $base =
                $segmento;

        }


        /*
        |--------------------------------------------------------------------------
        | Garantizar unicidad
        |--------------------------------------------------------------------------
        */

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


    /*
    |--------------------------------------------------------------------------
    | Actualizar slugs descendientes
    |--------------------------------------------------------------------------
    */

    private function actualizarSlugsDescendientes(
        Categoria $categoria
    ): void
    {

        $hijas =
            $this->repository
                ->getChildren(
                    $categoria
                );


        foreach (
            $hijas
            as $hija
        ) {

            $nuevoSlug =
                $this->generarSlug(

                    $hija
                        ->nombre,

                    $categoria
                        ->id_categoria,

                    $hija
                        ->id_categoria

                );


            if (
                $nuevoSlug
                !==
                $hija
                    ->slug
            ) {

                $hija =
                    $this->repository
                        ->update(

                            $hija,

                            [
                                'slug' =>
                                    $nuevoSlug
                            ]

                        );

            }


            /*
             * Continuar recursivamente.
             */
            $this
                ->actualizarSlugsDescendientes(
                    $hija
                );

        }

    }

}
