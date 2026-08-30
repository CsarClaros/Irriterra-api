<?php

namespace App\Http\Resources\Compras;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProveedorResource extends JsonResource
{
    /**
     * Transforma un proveedor.
     */
    public function toArray(
        Request $request
    ): array {

        return [

            'id_proveedor' =>
                $this->id_proveedor,

            'tipo_proveedor' =>
                $this->tipo_proveedor,

            'nombre_razon_social' =>
                $this->nombre_razon_social,

            'tipo_documento' =>
                $this->tipo_documento,

            'numero_documento' =>
                $this->numero_documento,

            'nombre_contacto' =>
                $this->nombre_contacto,

            'telefono' =>
                $this->telefono,

            'correo' =>
                $this->correo,

            'direccion' =>
                $this->direccion,

            'ciudad' =>
                $this->ciudad,

            'departamento' =>
                $this->departamento,

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