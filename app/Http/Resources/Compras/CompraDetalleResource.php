<?php

namespace App\Http\Resources\Compras;

use App\Http\Resources\Inventario\ProductoVarianteResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompraDetalleResource extends JsonResource
{
    /**
     * Transforma un detalle de compra.
     */
    public function toArray(
        Request $request
    ): array {

        return [

            'id_compra_detalle' =>
                $this->id_compra_detalle,

            'id_compra' =>
                $this->id_compra,

            'id_producto_variante' =>
                $this->id_producto_variante,

            'cantidad' =>
                $this->cantidad,

            'costo_unitario' =>
                $this->costo_unitario,

            'descuento' =>
                $this->descuento,

            'subtotal' =>
                $this->subtotal,

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