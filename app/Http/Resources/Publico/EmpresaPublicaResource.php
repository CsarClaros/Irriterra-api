<?php

namespace App\Http\Resources\Publico;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class EmpresaPublicaResource
    extends JsonResource
{

    public function toArray(
        Request $request
    ): array {

        return [

            'id_empresa' =>
                $this->id_empresa,

            'nombre' =>
                $this->nombre,

            'telefono' =>
                $this->telefono,

            'correo' =>
                $this->correo,

            'direccion' =>
                $this->direccion,

            'sitio_web' =>
                $this->sitio_web,

            'logo' =>
                $this->logo

                    ? asset(
                    'storage/'
                    .
                    ltrim(
                        $this->logo,
                        '/'
                    )
                )

                    : null

        ];

    }

}
