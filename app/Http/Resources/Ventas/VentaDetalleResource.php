<?php

namespace App\Http\Resources\Ventas;

use App\Http\Resources\Inventario\ProductoVarianteResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VentaDetalleResource extends JsonResource
{
    /**
     * Transforma un detalle de venta.
     */
    public function toArray(
        Request $request
    ): array {

        return [

            'id_venta_detalle' =>
                $this->id_venta_detalle,

            'id_venta' =>
                $this->id_venta,

            'id_producto_variante' =>
                $this->id_producto_variante,

            'cantidad' =>
                $this->cantidad,

            'precio_unitario' =>
                $this->precio_unitario,

            /*
             * costo_unitario no se expone aquí.
             * Posteriormente puede mostrarse mediante
             * un Resource administrativo protegido.
             */

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
