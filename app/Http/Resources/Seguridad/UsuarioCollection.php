<?php

namespace App\Http\Resources\Seguridad;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UsuarioCollection extends ResourceCollection
{
    /**
     * Transforma la colección.
     */
    public function toArray(Request $request): array
    {
        return [

            'data' => UsuarioResource::collection(

                $this->collection

            )

        ];
    }
}