<?php

namespace App\Http\Resources\Inventario;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class ProductoDocumentoResource
    extends JsonResource
{

    public function toArray(
        Request $request
    ): array
    {

        return [

            'id_producto_documento' =>
                $this->id_producto_documento,

            'id_producto' =>
                $this->id_producto,

            'tipo' =>
                $this->tipo,

            'nombre' =>
                $this->nombre,

            'archivo' =>
                $this->archivo,

            'es_publico' =>
                $this->es_publico,

            'orden' =>
                $this->orden,

            'observaciones' =>
                $this->observaciones,

            'estado_registro' =>
                $this->estado_registro,

            'producto' =>
                $this->whenLoaded(
                    'producto',
                    function () {

                        return [

                            'id_producto' =>
                                $this->producto
                                    ->id_producto,

                            'nombre' =>
                                $this->producto
                                    ->nombre

                        ];

                    }
                ),

            'created_at' =>
                $this->created_at,

            'updated_at' =>
                $this->updated_at

        ];

    }

}
