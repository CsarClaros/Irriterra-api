<?php

namespace App\Http\Resources\Seguridad;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermisoResource extends JsonResource
{
    /**
     * Transforma el recurso.
     */
    public function toArray(Request $request): array
    {
        return [

            'id_permiso' => $this->id_permiso,

            'nombre' => $this->nombre,

            'descripcion' => $this->descripcion,

            'estado_registro' => $this->estado_registro,

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at

        ];
    }
}