<?php

namespace App\Http\Resources\Organizacion;

use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;

class EmpresaResource extends BaseResource
{
    /**
     * Transforma el recurso.
     */
    public function toArray(Request $request): array
    {
        return [

            'id_empresa' => $this->id_empresa,

            'nombre' => $this->nombre,

            'nit' => $this->nit,

            'telefono' => $this->telefono,

            'correo' => $this->correo,

            'direccion' => $this->direccion,

            'sitio_web' => $this->sitio_web,

            'logo' => $this->logo,

            'observaciones' => $this->observaciones,

            'estado_registro' => $this->estado_registro,

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at

        ];
    }
}