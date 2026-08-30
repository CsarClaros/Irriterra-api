<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class BaseCollection extends ResourceCollection
{
    /**
     * Convierte la colección.
     */
    // abstract public function toArray(Request $request): array;

    /**
     * Información adicional.
     */
    public function with(Request $request): array
    {
        return [

            'success' => true,

            'total' => $this->collection->count(),

            'timestamp' => now()->toDateTimeString()

        ];
    }
}