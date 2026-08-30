<?php

namespace App\Http\Resources\Seguridad;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PermisoCollection extends ResourceCollection
{
    /**
     * Transforma la colección.
     */
    public function toArray(Request $request): array
    {
        return [

            'data' => PermisoResource::collection($this->collection)

        ];
    }
}