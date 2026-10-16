<?php

namespace App\Http\Resources\Inventario;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoVarianteResource extends JsonResource
{
    public function toArray(
        Request $request
    ): array
    {

        return [

            'id_producto_variante' =>
                $this->id_producto_variante,

            'id_producto' =>
                $this->id_producto,

            'id_marca' =>
                $this->id_marca,

            'nombre' =>
                $this->nombre,

            'sku' =>
                $this->sku,

            'codigo_comercial' =>
                $this->codigo_comercial,

            'unidad_medida' =>
                $this->unidad_medida,

            'tipo_control_inventario' =>
                $this->tipo_control_inventario,

            'descripcion' =>
                $this->descripcion,

            'observaciones' =>
                $this->observaciones,

            'estado_registro' =>
                $this->estado_registro,

            'usuario_creacion' =>
                $this->usuario_creacion,

            'usuario_modificacion' =>
                $this->usuario_modificacion,

            'marca' =>
                new MarcaResource(
                    $this->whenLoaded(
                        'marca'
                    )
                ),

            'producto' =>
                new ProductoResource(
                    $this->whenLoaded(
                        'producto'
                    )
                ),

            'created_at' =>
                $this->created_at,

            'updated_at' =>
                $this->updated_at

        ];
    }
}
