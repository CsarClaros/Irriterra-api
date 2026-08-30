<?php

namespace App\Http\Resources\Inventario;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrecioProductoVarianteResource extends JsonResource
{
    /**
     * Transforma el recurso.
     */
    public function toArray(
        Request $request
    ): array {

        return [

            'id_precio_producto_variante' =>
                $this->id_precio_producto_variante,

            'id_producto_variante' =>
                $this->id_producto_variante,

            'costo_compra' =>
                $this->when(

                    $request->user()
                        ?->tienePermiso(
                            'precio.costo_compra.ver'
                        ) ?? false,

                    $this->costo_compra

                ),

            'precio_minimo' =>
                $this->precio_minimo,

            'precio_venta' =>
                $this->precio_venta,

            'fecha_vigencia' =>
                $this->fecha_vigencia,

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
