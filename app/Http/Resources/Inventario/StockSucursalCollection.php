<?php

namespace App\Http\Resources\Inventario;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class StockSucursalCollection extends ResourceCollection
{
    /**
     * Transforma la colección.
     */
    public function toArray(Request $request): array
    {
        return [

            'data' => StockSucursalResource::collection(

                $this->collection

            )

        ];
    }
}