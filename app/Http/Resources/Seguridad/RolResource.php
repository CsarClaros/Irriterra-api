<?php

namespace App\Http\Resources\Seguridad;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RolResource extends JsonResource
{
    /**
     * Transforma el recurso en un arreglo.
     */
    public function toArray(Request $request): array
    {
        return [

            'id_rol' => $this->id_rol,

            'nombre' => $this->nombre,

            'descripcion' => $this->descripcion,

            'estado_registro' => $this->estado_registro,

            'nivel' => $this->nivel,

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at

        ];
    }
}
