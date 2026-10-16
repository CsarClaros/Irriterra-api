<?php

namespace App\Repositories\Inventario;

use App\Models\Inventario\MovimientoInventarioItem;

class MovimientoInventarioItemRepository
{
    public function create(
        array $data
    ): MovimientoInventarioItem
    {

        return MovimientoInventarioItem::create(
            $data
        );
    }
}
