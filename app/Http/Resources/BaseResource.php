<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class BaseResource extends JsonResource
{
    /**
     * Convierte el recurso en un arreglo.
     */
    // abstract public function toArray(Request $request): array;

    /**
     * Información adicional incluida en la respuesta.
     */
    public function with(Request $request): array
    {
        return [

            'success' => true,

            'timestamp' => now()->toDateTimeString()

        ];
    }
}