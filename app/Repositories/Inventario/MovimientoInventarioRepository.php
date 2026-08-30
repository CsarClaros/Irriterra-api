<?php

namespace App\Repositories\Inventario;

use App\Models\Inventario\MovimientoInventario;
use App\Models\Inventario\StockSucursal;

class MovimientoInventarioRepository
{
    /**
     * Lista movimientos activos.
     */
    public function getAll()
    {
        return MovimientoInventario::with([

            'stockSucursal.sucursal',

            'stockSucursal.productoVariante.producto.categoria'

        ])
            ->where('estado_registro', 'A')
            ->orderByDesc('fecha_movimiento')
            ->orderByDesc('id_movimiento_inventario')
            ->get();
    }

    /**
     * Busca por ID.
     */
    public function findById(int $id): MovimientoInventario
    {
        return MovimientoInventario::with([

            'stockSucursal.sucursal',

            'stockSucursal.productoVariante.producto.categoria'

        ])
            ->findOrFail($id);
    }

    /**
     * Obtiene y bloquea un registro de stock.
     */
    public function findStockForUpdate(
        int $idStockSucursal
    ): StockSucursal {

        return StockSucursal::where(
            'estado_registro',
            'A'
        )
            ->lockForUpdate()
            ->findOrFail($idStockSucursal);
    }

    /**
     * Actualiza la cantidad disponible.
     */
    public function updateStock(
        StockSucursal $stockSucursal,
        string $stockResultante
    ): StockSucursal {

        $stockSucursal->update([

            'stock_actual' => $stockResultante

        ]);

        return $stockSucursal->refresh();
    }

    /**
     * Registra un movimiento.
     */
    public function create(
        array $data
    ): MovimientoInventario {

        $movimientoInventario =
            MovimientoInventario::create($data);

        return $movimientoInventario->load([

            'stockSucursal.sucursal',

            'stockSucursal.productoVariante.producto.categoria'

        ]);
    }

    /**
     * Actualiza información complementaria del movimiento.
     */
    public function update(
        MovimientoInventario $movimientoInventario,
        array $data
    ): MovimientoInventario {

        $movimientoInventario->update($data);

        return $movimientoInventario->fresh([

            'stockSucursal.sucursal',

            'stockSucursal.productoVariante.producto.categoria'

        ]);
    }
}
