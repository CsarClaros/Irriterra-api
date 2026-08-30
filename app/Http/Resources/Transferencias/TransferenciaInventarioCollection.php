<?php

namespace App\Http\Resources\Transferencias;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TransferenciaInventarioCollection extends ResourceCollection
{
    public function toArray(
        Request $request
    ): array {

        return [

            'data' =>
                TransferenciaInventarioResource::collection(

                    $this->collection

                )

        ];
    }
}