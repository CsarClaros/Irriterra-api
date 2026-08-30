<?php

namespace App\Http\Resources\Reportes;

use App\Http\Resources\Inventario\ProductoVarianteResource;
use App\Http\Resources\Organizacion\SucursalResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReporteStockInventarioResource extends JsonResource
{
    /**
     * Transforma una fila del reporte de stock.
     */
    public function toArray(
        Request $request
    ): array {

        return [

            'id_stock_sucursal' =>
                $this[
                    'id_stock_sucursal'
                ],

            'id_sucursal' =>
                $this['id_sucursal'],

            'id_producto_variante' =>
                $this[
                    'id_producto_variante'
                ],

            'stock_actual' =>
                $this['stock_actual'],

            'stock_minimo' =>
                $this[
                    'stock_minimo'
                ] ?? null,

            'stock_maximo' =>
                $this[
                    'stock_maximo'
                ] ?? null,

            'estado_stock' =>
                $this[
                    'estado_stock'
                ] ?? null,

            'faltante_minimo' =>
                $this[
                    'faltante_minimo'
                ] ?? null,

            'costo_compra' =>
                $this[
                    'costo_compra'
                ] ?? null,

            'valor_inventario' =>
                $this[
                    'valor_inventario'
                ] ?? null,

            'tiene_precio_configurado' =>
                $this[
                    'tiene_precio_configurado'
                ] ?? null,

            'ubicacion_almacen' =>
                $this[
                    'ubicacion_almacen'
                ] ?? null,

            'sucursal' =>
                new SucursalResource(

                    $this['sucursal']

                ),

            'producto_variante' =>
                new ProductoVarianteResource(

                    $this[
                        'producto_variante'
                    ]

                )

        ];
    }
}