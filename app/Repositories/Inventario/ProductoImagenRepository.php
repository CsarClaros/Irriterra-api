<?php

namespace App\Repositories\Inventario;

use App\Models\Inventario\ProductoImagen;

class ProductoImagenRepository
{
    /**
     * Lista imágenes activas.
     */
    public function getAll()
    {
        return ProductoImagen::with([

            'producto.categoria'

        ])
            ->where('estado_registro', 'A')
            ->orderBy('id_producto')
            ->orderBy('orden')
            ->get();
    }

    /**
     * Busca por ID.
     */
    public function findById(int $id): ProductoImagen
    {
        return ProductoImagen::with([

            'producto.categoria'

        ])
            ->findOrFail($id);
    }

    /**
     * Registra una imagen.
     */
    public function create(array $data): ProductoImagen
    {
        $productoImagen = ProductoImagen::create($data);

        return $productoImagen->load([

            'producto.categoria'

        ]);
    }

    /**
     * Actualiza una imagen.
     */
    public function update(
        ProductoImagen $productoImagen,
        array          $data
    ): ProductoImagen
    {

        $productoImagen->update($data);

        return $productoImagen->fresh([

            'producto.categoria'

        ]);
    }

    /**
     * Desmarca las imágenes principales de un producto.
     */
    public function desmarcarPrincipal(
        int  $idProducto,
        ?int $idProductoVariante = null,
        ?int $exceptoId = null
    ): int
    {

        $query =
            ProductoImagen::where(
                'id_producto',
                $idProducto
            )
                ->where(
                    'estado_registro',
                    'A'
                );


        /*
         * Galería general o galería
         * específica de variante.
         */

        if (
            $idProductoVariante === null
        ) {

            $query->whereNull(
                'id_producto_variante'
            );

        } else {

            $query->where(
                'id_producto_variante',
                $idProductoVariante
            );

        }


        $query->where(
            'es_principal',
            true
        );


        if (
            $exceptoId !== null
        ) {

            $query->where(
                'id_producto_imagen',
                '<>',
                $exceptoId
            );

        }


        return $query->update([

            'es_principal' =>
                false

        ]);
    }

    /**
     * Eliminación lógica.
     */
    public function delete(
        ProductoImagen $productoImagen
    ): bool
    {

        return $productoImagen
            ->update([

                'estado_registro' =>
                    'I',

                'es_principal' =>
                    false

            ]);

    }

    /*
|--------------------------------------------------------------------------
| Verificar orden ocupado
|--------------------------------------------------------------------------
*/

    public function existeOrdenEnGaleria(
        int  $idProducto,
        ?int $idProductoVariante,
        int  $orden,
        ?int $exceptoId = null
    ): bool
    {

        $query =
            ProductoImagen::query()
                ->where(
                    'id_producto',
                    $idProducto
                )
                ->where(
                    'orden',
                    $orden
                )
                ->where(
                    'estado_registro',
                    'A'
                );


        /*
        |--------------------------------------------------------------------------
        | Galería
        |--------------------------------------------------------------------------
        */

        if (
            $idProductoVariante
            === null
        ) {

            $query
                ->whereNull(
                    'id_producto_variante'
                );

        } else {

            $query
                ->where(
                    'id_producto_variante',
                    $idProductoVariante
                );

        }


        /*
        |--------------------------------------------------------------------------
        | Excluir imagen editada
        |--------------------------------------------------------------------------
        */

        if (
            $exceptoId
            !== null
        ) {

            $query
                ->where(
                    'id_producto_imagen',
                    '<>',
                    $exceptoId
                );

        }


        return $query
            ->exists();

    }

    /*
|--------------------------------------------------------------------------
| Asignar primera imagen como principal
|--------------------------------------------------------------------------
*/

    public function asignarPrimeraComoPrincipal(
        int  $idProducto,
        ?int $idProductoVariante
    ): ?ProductoImagen
    {

        $query =
            ProductoImagen::query()
                ->where(
                    'id_producto',
                    $idProducto
                )
                ->where(
                    'estado_registro',
                    'A'
                );


        if (
            $idProductoVariante
            === null
        ) {

            $query
                ->whereNull(
                    'id_producto_variante'
                );

        } else {

            $query
                ->where(
                    'id_producto_variante',
                    $idProductoVariante
                );

        }


        $imagen =
            $query
                ->orderBy(
                    'orden'
                )
                ->first();


        if (
            !$imagen
        ) {

            return null;

        }


        $imagen->update([
            'es_principal' =>
                true
        ]);


        return $imagen;

    }

}
