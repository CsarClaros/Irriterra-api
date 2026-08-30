<?php

namespace App\Http\Resources\Inventario;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoriaResource extends JsonResource
{
    /**
     * Convierte el recurso en un arreglo.
     */
    public function toArray(Request $request): array
    {
        return [

            'id_categoria' => $this->id_categoria,

            'nombre' => $this->nombre,

            'descripcion' => $this->descripcion,

            'observaciones' => $this->observaciones,

            'estado_registro' => $this->estado_registro,

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at

        ];
    }
}