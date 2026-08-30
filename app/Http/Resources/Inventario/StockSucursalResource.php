<?php

namespace App\Http\Resources\Inventario;

use App\Http\Resources\Organizacion\SucursalResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockSucursalResource extends JsonResource
{
    /**
     * Transforma el recurso.
     */
    public function toArray(Request $request): array
    {
        return [

            'id_stock_sucursal' =>
                $this->id_stock_sucursal,

            'id_sucursal' => $this->id_sucursal,

            'id_producto_variante' =>
                $this->id_producto_variante,

            'stock_actual' => $this->stock_actual,

            'stock_minimo' => $this->stock_minimo,

            'stock_maximo' => $this->stock_maximo,

            'ubicacion_almacen' =>
                $this->ubicacion_almacen,

            'observaciones' => $this->observaciones,

            'estado_registro' => $this->estado_registro,

            'usuario_creacion' => $this->usuario_creacion,

            'usuario_modificacion' =>
                $this->usuario_modificacion,

            'sucursal' => new SucursalResource(

                $this->whenLoaded('sucursal')

            ),

            'producto_variante' =>
                new ProductoVarianteResource(

                    $this->whenLoaded(
                        'productoVariante'
                    )

                ),

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at

        ];
    }
}