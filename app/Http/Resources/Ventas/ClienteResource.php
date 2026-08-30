<?php

namespace App\Http\Resources\Ventas;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource
{
    /**
     * Transforma el cliente.
     */
    public function toArray(
        Request $request
    ): array {

        return [

            'id_cliente' =>
                $this->id_cliente,

            'tipo_cliente' =>
                $this->tipo_cliente,

            'nombre_razon_social' =>
                $this->nombre_razon_social,

            'tipo_documento' =>
                $this->tipo_documento,

            'numero_documento' =>
                $this->numero_documento,

            'telefono' =>
                $this->telefono,

            'correo' =>
                $this->correo,

            'direccion' =>
                $this->direccion,

            'observaciones' =>
                $this->observaciones,

            'estado_registro' =>
                $this->estado_registro,

            'usuario_creacion' =>
                $this->usuario_creacion,

            'usuario_modificacion' =>
                $this->usuario_modificacion,

            'created_at' =>
                $this->created_at,

            'updated_at' =>
                $this->updated_at

        ];
    }
}