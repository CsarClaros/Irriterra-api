<?php

namespace App\Repositories\Reportes;

use App\Models\Inventario\MovimientoInventario;
use App\Models\Inventario\PrecioProductoVariante;
use App\Models\Inventario\StockSucursal;
use Illuminate\Database\Eloquent\Collection;

class ReporteInventarioRepository
{
    /**
     * Obtiene stock aplicando filtros.
     */
    public function getStock(
        array $filters
    ): Collection {

        $query =
            StockSucursal::with([

                'sucursal',

                'productoVariante.producto.categoria'

            ])
                ->where(
                    'estado_registro',
                    'A'
                );

        if (
            ! empty(
                $filters['id_sucursal']
            )
        ) {

            $query->where(

                'id_sucursal',

                $filters['id_sucursal']

            );

        }

        if (
            ! empty(
                $filters['id_producto_variante']
            )
        ) {

            $query->where(

                'id_producto_variante',

                $filters[
                    'id_producto_variante'
                ]

            );

        }

        if (
            ! empty(
                $filters['id_categoria']
            )
        ) {

            $query->whereHas(

                'productoVariante.producto',

                function ($productoQuery) use (
                    $filters
                ): void {

                    $productoQuery->where(

                        'id_categoria',

                        $filters['id_categoria']

                    );

                }

            );

        }

        $estadoStock =
            $filters['estado_stock']
            ?? 'TODOS';

        if (
            $estadoStock === 'AGOTADO'
        ) {

            $query->where(
                'stock_actual',
                '<=',
                0
            );

        }

        if (
            $estadoStock === 'BAJO'
        ) {

            $query
                ->where(
                    'stock_actual',
                    '>',
                    0
                )
                ->whereColumn(

                    'stock_actual',

                    '<=',

                    'stock_minimo'

                );

        }

        if (
            $estadoStock === 'NORMAL'
        ) {

            $query->whereColumn(

                'stock_actual',

                '>',

                'stock_minimo'

            );

        }

        return $query
            ->orderBy(
                'id_sucursal'
            )
            ->orderBy(
                'id_producto_variante'
            )
            ->get();
    }

    /**
     * Obtiene los costos activos de las variantes.
     */
    public function getPreciosByVariantes(
        array $idsProductoVariante
    ): Collection {

        return PrecioProductoVariante::whereIn(

            'id_producto_variante',

            $idsProductoVariante

        )
            ->where(
                'estado_registro',
                'A'
            )
            ->get()
            ->keyBy(
                'id_producto_variante'
            );
    }

    /**
     * Obtiene movimientos de inventario.
     */
    public function getMovimientos(
        array $filters
    ): Collection {

        $query =
            MovimientoInventario::with([

                'stockSucursal.sucursal',

                'stockSucursal.productoVariante.producto.categoria'

            ])
                ->where(
                    'estado_registro',
                    'A'
                );

        if (
            ! empty(
                $filters['id_sucursal']
            )
        ) {

            $query->whereHas(

                'stockSucursal',

                function ($stockQuery) use (
                    $filters
                ): void {

                    $stockQuery->where(

                        'id_sucursal',

                        $filters['id_sucursal']

                    );

                }

            );

        }

        if (
            ! empty(
                $filters['id_producto_variante']
            )
        ) {

            $query->whereHas(

                'stockSucursal',

                function ($stockQuery) use (
                    $filters
                ): void {

                    $stockQuery->where(

                        'id_producto_variante',

                        $filters[
                            'id_producto_variante'
                        ]

                    );

                }

            );

        }

        if (
            ! empty(
                $filters['tipo_movimiento']
            )
        ) {

            $query->where(

                'tipo_movimiento',

                $filters['tipo_movimiento']

            );

        }

        if (
            ! empty(
                $filters['tipo_referencia']
            )
        ) {

            $query->where(

                'tipo_referencia',

                $filters['tipo_referencia']

            );

        }

        if (
            ! empty(
                $filters['fecha_desde']
            )
        ) {

            $query->whereDate(

                'fecha_movimiento',

                '>=',

                $filters['fecha_desde']

            );

        }

        if (
            ! empty(
                $filters['fecha_hasta']
            )
        ) {

            $query->whereDate(

                'fecha_movimiento',

                '<=',

                $filters['fecha_hasta']

            );

        }

        return $query
            ->orderBy(
                'fecha_movimiento'
            )
            ->orderBy(
                'id_movimiento_inventario'
            )
            ->get();
    }
}