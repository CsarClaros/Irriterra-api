<?php

namespace App\Http\Resources\Inventario;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;


class MarcaCollection extends ResourceCollection
{

    public function toArray(
        Request $request
    ): array
    {

        return [

            'data' =>
                MarcaResource::collection(
                    $this->collection
                )

        ];

    }

}
