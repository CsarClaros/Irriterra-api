<?php

namespace App\Repositories\Inventario;

use App\Models\Inventario\Producto;


class ProductoRepository
{

    /**
     * Lista productos.
     */
    public function getAll(
        bool $incluirInactivos = false
    )
    {

        $query =
            Producto::with(
                'categoria'
            );


        if (
            !$incluirInactivos
        ) {

            $query->where(
                'estado_registro',
                'A'
            );

        }


        return $query
            ->orderBy(
                'nombre'
            )
            ->get();

    }


    /**
     * Busca por ID.
     */
    public function findById(
        int $id
    ): Producto
    {

        return Producto::with(
            'categoria'
        )
            ->findOrFail(
                $id
            );

    }


    /**
     * Registra un producto.
     */
    public function create(
        array $data
    ): Producto
    {

        $producto =
            Producto::create(
                $data
            );


        return $producto
            ->load(
                'categoria'
            );

    }


    /**
     * Actualiza un producto.
     */
    public function update(
        Producto $producto,
        array    $data
    ): Producto
    {

        $producto->update(
            $data
        );


        return $producto
            ->fresh([
                'categoria'
            ]);

    }


    /**
     * Desactivación lógica.
     */
    public function delete(
        Producto $producto
    ): bool
    {

        return $producto
            ->update([

                'estado_registro' =>
                    'I'

            ]);

    }


    /**
     * Reactiva un producto.
     */
    public function reactivate(
        Producto $producto
    ): bool
    {

        return $producto
            ->update([

                'estado_registro' =>
                    'A'

            ]);

    }

}
