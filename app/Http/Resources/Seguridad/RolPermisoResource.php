<?php

namespace App\Http\Resources\Seguridad;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RolPermisoResource extends JsonResource
{
    /**
     * Transforma el recurso.
     */
    public function toArray(Request $request): array
    {
        return [

            'id_rol_permiso' => $this->id_rol_permiso,

            'rol' => $this->whenLoaded('rol', function () {

                return [

                    'id_rol' => $this->rol->id_rol,

                    'nombre' => $this->rol->nombre

                ];
            }),

            'permiso' => $this->whenLoaded('permiso', function () {

                return [

                    'id_permiso' => $this->permiso->id_permiso,

                    'nombre' => $this->permiso->nombre

                ];
            }),

            'estado_registro' => $this->estado_registro,

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at

        ];
    }
}
