<?php

namespace App\Http\Resources\Inventario;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class MarcaResource extends JsonResource
{

    public function toArray(
        Request $request
    ): array
    {

        return [

            'id_marca' =>
                $this->id_marca,

            'nombre' =>
                $this->nombre,

            'slug' =>
                $this->slug,

            'pais' =>
                $this->pais,

            'logo' =>
                $this->logo,

            'sitio_web' =>
                $this->sitio_web,

            'orden' =>
                $this->orden,

            'observaciones' =>
                $this->observaciones,

            'estado_registro' =>
                $this->estado_registro,

            'created_at' =>
                $this->created_at,

            'updated_at' =>
                $this->updated_at

        ];

    }

}
