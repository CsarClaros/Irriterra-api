<?php

namespace App\Http\Resources\Inventario;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovimientoInventarioResource extends JsonResource
{
    /**
     * Transforma el recurso.
     */
    public function toArray(Request $request): array
    {
        return [

            'id_movimiento_inventario' =>
                $this->id_movimiento_inventario,

            'id_stock_sucursal' =>
                $this->id_stock_sucursal,

            'codigo_movimiento' =>
                $this->codigo_movimiento,

            'tipo_movimiento' =>
                $this->tipo_movimiento,

            'cantidad' => $this->cantidad,

            'stock_anterior' =>
                $this->stock_anterior,

            'stock_resultante' =>
                $this->stock_resultante,

            'motivo' => $this->motivo,

            'tipo_referencia' =>
                $this->tipo_referencia,

            'id_referencia' =>
                $this->id_referencia,

            'fecha_movimiento' =>
                $this->fecha_movimiento,

            'observaciones' =>
                $this->observaciones,

            'estado_registro' =>
                $this->estado_registro,

            'usuario_creacion' =>
                $this->usuario_creacion,

            'usuario_modificacion' =>
                $this->usuario_modificacion,

            'stock_sucursal' =>
                new StockSucursalResource(

                    $this->whenLoaded(
                        'stockSucursal'
                    )

                ),

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at

        ];
    }
}