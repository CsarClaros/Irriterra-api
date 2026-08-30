<?php

namespace App\Http\Resources\Organizacion;

use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;

class SucursalResource extends BaseResource
{
    /**
     * Transforma el recurso.
     */
    public function toArray(Request $request): array
    {
        return [

            'id_sucursal' => $this->id_sucursal,

            'id_empresa' => $this->id_empresa,

            'codigo' => $this->codigo,

            'nombre' => $this->nombre,

            'departamento' => $this->departamento,

            'ciudad' => $this->ciudad,

            'direccion' => $this->direccion,

            'telefono' => $this->telefono,

            'correo' => $this->correo,

            'latitud' => $this->latitud,

            'longitud' => $this->longitud,

            'observaciones' => $this->observaciones,

            'estado_registro' => $this->estado_registro,

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at

        ];
    }
}