<?php

namespace App\Http\Resources\Reportes;

use App\Http\Resources\Inventario\StockSucursalResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReporteMovimientoInventarioResource extends JsonResource
{
    /**
     * Transforma una fila del kardex.
     */
    public function toArray(
        Request $request
    ): array {

        return [

            'id_movimiento_inventario' =>
                $this[
                    'id_movimiento_inventario'
                ],

            'codigo_movimiento' =>
                $this[
                    'codigo_movimiento'
                ],

            'id_stock_sucursal' =>
                $this[
                    'id_stock_sucursal'
                ],

            'tipo_movimiento' =>
                $this[
                    'tipo_movimiento'
                ],

            'cantidad' =>
                $this['cantidad'],

            'stock_anterior' =>
                $this[
                    'stock_anterior'
                ],

            'stock_resultante' =>
                $this[
                    'stock_resultante'
                ],

            'motivo' =>
                $this['motivo'],

            'tipo_referencia' =>
                $this[
                    'tipo_referencia'
                ],

            'id_referencia' =>
                $this[
                    'id_referencia'
                ],

            'fecha_movimiento' =>
                $this[
                    'fecha_movimiento'
                ],

            'observaciones' =>
                $this[
                    'observaciones'
                ],

            'stock_sucursal' =>
                new StockSucursalResource(

                    $this[
                        'stock_sucursal'
                    ]

                )

        ];
    }
}