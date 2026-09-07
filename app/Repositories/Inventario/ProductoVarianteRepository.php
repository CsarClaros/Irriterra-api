<?php

namespace App\Repositories\Inventario;

use App\Models\Inventario\ProductoVariante;


class ProductoVarianteRepository
{

    /**
     * Lista variantes activas.
     */
    public function getAll()
    {

        return ProductoVariante::with([

            'producto.categoria',

            'marca'

        ])
            ->where(
                'estado_registro',
                'A'
            )
            ->orderBy(
                'sku'
            )
            ->get();

    }


    /**
     * Busca por ID.
     */
    public function findById(
        int $id
    ): ProductoVariante
    {

        return ProductoVariante::with([

            'producto.categoria',

            'marca'

        ])
            ->findOrFail(
                $id
            );

    }


    /**
     * Registra una variante.
     */
    public function create(
        array $data
    ): ProductoVariante
    {

        $productoVariante =
            ProductoVariante::create(
                $data
            );


        return $productoVariante
            ->load([

                'producto.categoria',

                'marca'

            ]);

    }


    /**
     * Actualiza una variante.
     */
    public function update(
        ProductoVariante $productoVariante,
        array            $data
    ): ProductoVariante
    {

        $productoVariante->update(
            $data
        );


        return $productoVariante
            ->fresh([

                'producto.categoria',

                'marca'

            ]);

    }


    /**
     * Eliminación lógica.
     */
    public function delete(
        ProductoVariante $productoVariante
    ): bool
    {

        return $productoVariante
            ->update([

                'estado_registro' =>
                    'I'

            ]);

    }

}
