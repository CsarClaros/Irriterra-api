<?php

namespace App\Http\Resources\Transferencias;

use App\Http\Resources\Inventario\ProductoVarianteResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferenciaInventarioDetalleResource extends JsonResource
{
    /**
     * Transforma el detalle.
     */
    public function toArray(
        Request $request
    ): array {

        return [

            'id_transferencia_inventario_detalle' =>
                $this->id_transferencia_inventario_detalle,

            'id_transferencia_inventario' =>
                $this->id_transferencia_inventario,

            'id_producto_variante' =>
                $this->id_producto_variante,

            'cantidad' =>
                $this->cantidad,

            'observaciones' =>
                $this->observaciones,

            'estado_registro' =>
                $this->estado_registro,

            'usuario_creacion' =>
                $this->usuario_creacion,

            'usuario_modificacion' =>
                $this->usuario_modificacion,

            'producto_variante' =>
                new ProductoVarianteResource(

                    $this->whenLoaded(
                        'productoVariante'
                    )

                ),

            'created_at' =>
                $this->created_at,

            'updated_at' =>
                $this->updated_at

        ];
    }
}