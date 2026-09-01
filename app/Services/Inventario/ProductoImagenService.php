<?php

namespace App\Services\Inventario;

use App\Models\Inventario\ProductoImagen;
use App\Repositories\Inventario\ProductoImagenRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Illuminate\Validation\ValidationException;

class ProductoImagenService
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly ProductoImagenRepository $repository
    )
    {
    }


    /**
     * Lista imágenes activas.
     */
    public function index()
    {
        return $this->repository
            ->getAll();
    }


    /**
     * Muestra una imagen.
     */
    public function show(
        int $id
    ): ProductoImagen
    {

        return $this->repository
            ->findById(
                $id
            );
    }


    /**
     * Registra una imagen.
     */
    public function store(
        array $data
    ): ProductoImagen
    {

        /*
        |--------------------------------------------------------------------------
        | Archivo
        |--------------------------------------------------------------------------
        */

        $archivo =
            $data['imagen'];

        unset(
            $data['imagen']
        );

        $idProducto =
            (int)
            $data['id_producto'];


        $idProductoVariante =
            $data['id_producto_variante']
            ?? null;


        $orden =
            (int)
            $data['orden'];


        $this
            ->validarOrdenDisponible(
                $idProducto,
                $idProductoVariante,
                $orden
            );

        /*
        |--------------------------------------------------------------------------
        | Almacenamiento
        |--------------------------------------------------------------------------
        */

        $directorio =
            $this->construirDirectorio(

                $data['id_producto'],

                $data['id_producto_variante']
                ?? null

            );

        $ruta =
            $archivo->store(

                $directorio,

                'public'

            );


        $data['ruta_imagen'] =
            $ruta;


        $data['estado_registro'] =
            'A';


        try {

            return DB::transaction(
                function () use (
                    $data
                ) {

                    /*
                     * Solamente una imagen
                     * puede ser principal.
                     */

                    if (
                        (bool)(
                            $data['es_principal']
                            ?? false
                        )
                    ) {

                        $this->repository
                            ->desmarcarPrincipal(

                                $data['id_producto'],

                                $data['id_producto_variante'] ?? null

                            );

                    }


                    return $this->repository
                        ->create(
                            $data
                        );

                }
            );

        } catch (Throwable $exception) {

            /*
             * Si falla la BD no dejamos
             * un archivo huérfano.
             */

            Storage::disk(
                'public'
            )
                ->delete(
                    $ruta
                );


            throw $exception;

        }
    }


    /**
     * Actualiza una imagen.
     */
    public function update(
        ProductoImagen $productoImagen,
        array          $data
    ): ProductoImagen
    {

        $idProducto =
            $data['id_producto']
            ??
            $productoImagen
                ->id_producto;

        $idProductoVariante =
            array_key_exists(
                'id_producto_variante',
                $data
            )

                ? $data['id_producto_variante']

                : $productoImagen
                ->id_producto_variante;

        $rutaAnterior =
            $productoImagen
                ->ruta_imagen;


        $rutaNueva =
            null;


        $orden =
            array_key_exists(
                'orden',
                $data
            )

                ? (int)
            $data['orden']

                : (int)
            $productoImagen->orden;

        $this
            ->validarOrdenDisponible(
                $idProducto,
                $idProductoVariante,
                $orden,
                $productoImagen
                    ->id_producto_imagen
            );

        /*
        |--------------------------------------------------------------------------
        | Nueva imagen
        |--------------------------------------------------------------------------
        */

        if (
            isset(
                $data['imagen']
            )
        ) {

            $archivo =
                $data['imagen'];


            unset(
                $data['imagen']
            );


            $directorio =
                $this->construirDirectorio(

                    $idProducto,

                    $idProductoVariante

                );


            $rutaNueva =
                $archivo->store(

                    $directorio,

                    'public'

                );


            $data['ruta_imagen'] =
                $rutaNueva;

        }


        try {

            $resultado =
                DB::transaction(
                    function () use (
                        $productoImagen,
                        $data,
                        $idProducto,
                        $idProductoVariante
                    ) {

                        $esPrincipal =
                            array_key_exists(
                                'es_principal',
                                $data
                            )

                                ? (bool)
                            $data['es_principal']

                                : (bool)
                            $productoImagen
                                ->es_principal;


                        if (
                            $esPrincipal
                        ) {

                            $this->repository
                                ->desmarcarPrincipal(

                                    $idProducto,

                                    $idProductoVariante,

                                    $productoImagen
                                        ->id_producto_imagen

                                );

                        }


                        return $this->repository
                            ->update(

                                $productoImagen,

                                $data

                            );

                    }
                );

        } catch (Throwable $exception) {

            if (
                $rutaNueva !== null
            ) {

                Storage::disk(
                    'public'
                )
                    ->delete(
                        $rutaNueva
                    );

            }


            throw $exception;

        }


        /*
        |--------------------------------------------------------------------------
        | Eliminar archivo reemplazado
        |--------------------------------------------------------------------------
        |
        | Solo eliminamos rutas creadas
        | por este nuevo sistema.
        |
        */

        if (
            $rutaNueva !== null
            &&
            is_string(
                $rutaAnterior
            )
            &&
            str_starts_with(
                $rutaAnterior,
                'productos/'
            )
        ) {

            Storage::disk(
                'public'
            )
                ->delete(
                    $rutaAnterior
                );

        }


        return $resultado;
    }


    /**
     * Eliminación lógica.
     */
    public function destroy(
        ProductoImagen $productoImagen
    ): bool {

        return DB::transaction(
            function () use (
                $productoImagen
            ) {

                /*
                |--------------------------------------------------------------------------
                | Datos antes de eliminar
                |--------------------------------------------------------------------------
                */

                $eraPrincipal =
                    (bool)
                    $productoImagen
                        ->es_principal;


                $idProducto =
                    (int)
                    $productoImagen
                        ->id_producto;


                $idProductoVariante =
                    $productoImagen
                        ->id_producto_variante;


                /*
                |--------------------------------------------------------------------------
                | Eliminación lógica
                |--------------------------------------------------------------------------
                */

                $eliminado =
                    $this
                        ->repository
                        ->delete(
                            $productoImagen
                        );


                /*
                |--------------------------------------------------------------------------
                | Reasignar principal
                |--------------------------------------------------------------------------
                */

                if (
                    $eliminado
                    &&
                    $eraPrincipal
                ) {

                    $this
                        ->repository
                        ->asignarPrimeraComoPrincipal(
                            $idProducto,
                            $idProductoVariante
                        );

                }


                return $eliminado;

            }
        );

    }

    /**
     * Construye el directorio donde
     * se almacenará la imagen.
     */
    private function construirDirectorio(
        int  $idProducto,
        ?int $idProductoVariante = null
    ): string
    {

        $directorio =
            'productos/'
            . $idProducto;


        if (
            $idProductoVariante !== null
        ) {

            $directorio .=
                '/variantes/'
                . $idProductoVariante;

        }


        return $directorio;
    }

    /*
|--------------------------------------------------------------------------
| Validar orden disponible
|--------------------------------------------------------------------------
*/

    private function validarOrdenDisponible(
        int  $idProducto,
        ?int $idProductoVariante,
        int  $orden,
        ?int $exceptoId = null
    ): void
    {

        $ocupado =
            $this
                ->repository
                ->existeOrdenEnGaleria(
                    $idProducto,
                    $idProductoVariante,
                    $orden,
                    $exceptoId
                );


        if (
            $ocupado
        ) {

            throw ValidationException::withMessages([
                'orden' => [
                    'El orden seleccionado ya está ocupado en esta galería.'
                ]
            ]);

        }

    }
}
