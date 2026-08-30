<?php

namespace App\Repositories\Inventario;

use App\Models\Inventario\StockSucursal;

class StockSucursalRepository
{
    /**
     * Lista registros de stock activos.
     */
    public function getAll()
    {
        return StockSucursal::with([

            'sucursal',

            'productoVariante.producto.categoria'

        ])
            ->where('estado_registro', 'A')
            ->orderBy('id_sucursal')
            ->orderBy('id_producto_variante')
            ->get();
    }

    /**
     * Busca por ID.
     */
    public function findById(int $id): StockSucursal
    {
        return StockSucursal::with([

            'sucursal',

            'productoVariante.producto.categoria'

        ])
            ->findOrFail($id);
    }

    /**
     * Registra stock por sucursal.
     */
    public function create(array $data): StockSucursal
    {
        $stockSucursal = StockSucursal::create($data);

        return $stockSucursal->load([

            'sucursal',

            'productoVariante.producto.categoria'

        ]);
    }

    /**
     * Actualiza stock por sucursal.
     */
    public function update(
        StockSucursal $stockSucursal,
        array $data
    ): StockSucursal {

        $stockSucursal->update($data);

        return $stockSucursal->fresh([

            'sucursal',

            'productoVariante.producto.categoria'

        ]);
    }

    /**
     * Eliminación lógica.
     */
    public function delete(
        StockSucursal $stockSucursal
    ): bool {

        return $stockSucursal->update([

            'estado_registro' => 'I'

        ]);
    }
}