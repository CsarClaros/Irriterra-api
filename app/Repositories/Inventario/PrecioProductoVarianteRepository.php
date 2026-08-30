<?php

namespace App\Repositories\Inventario;

use App\Models\Inventario\PrecioProductoVariante;

class PrecioProductoVarianteRepository
{
    /**
     * Lista precios activos.
     */
    public function getAll()
    {
        return PrecioProductoVariante::with([

            'productoVariante.producto.categoria'

        ])
            ->where('estado_registro', 'A')
            ->orderBy('id_producto_variante')
            ->get();
    }

    /**
     * Busca por ID.
     */
    public function findById(
        int $id
    ): PrecioProductoVariante {

        return PrecioProductoVariante::with([

            'productoVariante.producto.categoria'

        ])
            ->findOrFail($id);
    }

    /**
     * Registra precios para una variante.
     */
    public function create(
        array $data
    ): PrecioProductoVariante {

        $precioProductoVariante =
            PrecioProductoVariante::create($data);

        return $precioProductoVariante->load([

            'productoVariante.producto.categoria'

        ]);
    }

    /**
     * Actualiza precios de una variante.
     */
    public function update(
        PrecioProductoVariante $precioProductoVariante,
        array $data
    ): PrecioProductoVariante {

        $precioProductoVariante->update($data);

        return $precioProductoVariante->fresh([

            'productoVariante.producto.categoria'

        ]);
    }

    /**
     * Eliminación lógica.
     */
    public function delete(
        PrecioProductoVariante $precioProductoVariante
    ): bool {

        return $precioProductoVariante->update([

            'estado_registro' => 'I'

        ]);
    }
}