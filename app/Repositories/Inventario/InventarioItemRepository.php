<?php

namespace App\Repositories\Inventario;

use App\Models\Inventario\InventarioItem;
use Illuminate\Database\Eloquent\Collection;

class InventarioItemRepository
{
    public function getAll(): Collection
    {
        return InventarioItem::query()
            ->where(
                'estado_registro',
                'A'
            )
            ->with([
                'productoVariante',
                'stockSucursal.sucursal'
            ])
            ->orderByDesc(
                'id_inventario_item'
            )
            ->get();
    }

    public function findById(
        int $id
    ): InventarioItem
    {

        return InventarioItem::query()
            ->where(
                'estado_registro',
                'A'
            )
            ->with([
                'productoVariante',
                'stockSucursal.sucursal',
                'movimientos.movimientoInventario'
            ])
            ->findOrFail($id);
    }

    public function findByIdForUpdate(
        int $id
    ): InventarioItem
    {

        return InventarioItem::query()
            ->where(
                'estado_registro',
                'A'
            )
            ->lockForUpdate()
            ->findOrFail($id);
    }

    public function create(
        array $data
    ): InventarioItem
    {

        return InventarioItem::create(
            $data
        );
    }

    public function update(
        InventarioItem $item,
        array          $data
    ): InventarioItem
    {

        $item->fill($data);

        $item->save();

        return $item->refresh();
    }
}
