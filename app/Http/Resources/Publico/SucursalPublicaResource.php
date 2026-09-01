<?php

namespace App\Http\Resources\Publico;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class SucursalPublicaResource
    extends JsonResource
{

    public function toArray(
        Request $request
    ): array
    {

        return [

            'id_sucursal' =>
                $this->id_sucursal,

            'codigo' =>
                $this->codigo,

            'nombre' =>
                $this->nombre,

            'departamento' =>
                $this->departamento,

            'ciudad' =>
                $this->ciudad,

            'direccion' =>
                $this->direccion,

            'telefono' =>
                $this->telefono,

            'correo' =>
                $this->correo,

            'latitud' =>
                $this->latitud !== null
                    ? (float)$this->latitud
                    : null,

            'longitud' =>
                $this->longitud !== null
                    ? (float)$this->longitud
                    : null,

            'url_maps' =>
                $this->url_maps

        ];

    }

}
