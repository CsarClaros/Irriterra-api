<?php

namespace App\Http\Resources\Compras;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProveedorCollection extends ResourceCollection
{
    /**
     * Transforma la colección.
     */
    public function toArray(
        Request $request
    ): array {

        return [

            'data' =>
                ProveedorResource::collection(

                    $this->collection

                )

        ];
    }
}