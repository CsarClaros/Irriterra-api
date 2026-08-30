<?php

namespace App\Repositories\Reportes;

use App\Models\Compras\Compra;
use App\Models\Compras\CompraDetalle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ReporteComprasRepository
{
    /**
     * Obtiene compras aplicando filtros.
     */
    public function getCompras(
        array $filters
    ): Collection {

        $query =
            Compra::with([

                'sucursal',

                'proveedor',

                'comprador',

                'detalles.productoVariante.producto.categoria'

            ])
                ->where(
                    'estado_registro',
                    'A'
                );

        $this->applyFilters(
            $query,
            $filters
        );

        return $query
            ->orderByDesc(
                'fecha_compra'
            )
            ->orderByDesc(
                'id_compra'
            )
            ->get();
    }

    /**
     * Obtiene productos más comprados.
     *
     * Solo considera compras recibidas.
     */
    public function getProductosMasComprados(
        array $filters
    ): Collection {

        $limite =
            $filters['limite'] ?? 10;

        $query =
            CompraDetalle::query()
                ->join(
                    'compra',
                    'compra.id_compra',
                    '=',
                    'compra_detalle.id_compra'
                )
                ->where(
                    'compra.estado_registro',
                    'A'
                )
                ->where(
                    'compra_detalle.estado_registro',
                    'A'
                )
                ->where(
                    'compra.estado_compra',
                    Compra::RECIBIDA
                );

        $this->applyDetalleFilters(
            $query,
            $filters
        );

        return $query
            ->select(
                'compra_detalle.id_producto_variante'
            )
            ->selectRaw(
                'SUM(compra_detalle.cantidad) AS cantidad'
            )
            ->selectRaw(
                'SUM(compra_detalle.subtotal) AS total'
            )
            ->selectRaw(
                'COUNT(DISTINCT compra.id_compra) AS operaciones'
            )
            ->groupBy(
                'compra_detalle.id_producto_variante'
            )
            ->orderByDesc(
                'cantidad'
            )
            ->limit(
                $limite
            )
            ->get();
    }

    private function applyFilters(
        Builder $query,
        array $filters
    ): void {

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
                $filters['id_proveedor']
            )
        ) {

            $query->where(

                'id_proveedor',

                $filters['id_proveedor']

            );

        }

        if (
            ! empty(
                $filters['id_usuario_comprador']
            )
        ) {

            $query->where(

                'id_usuario_comprador',

                $filters[
                    'id_usuario_comprador'
                ]

            );

        }

        if (
            ! empty(
                $filters['estado_compra']
            )
        ) {

            $query->where(

                'estado_compra',

                $filters['estado_compra']

            );

        }

        if (
            ! empty(
                $filters['id_producto_variante']
            )
        ) {

            $query->whereHas(

                'detalles',

                function ($detalleQuery) use (
                    $filters
                ): void {

                    $detalleQuery->where(

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
                $filters['fecha_desde']
            )
        ) {

            $query->whereDate(

                'fecha_compra',

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

                'fecha_compra',

                '<=',

                $filters['fecha_hasta']

            );

        }
    }

    private function applyDetalleFilters(
        Builder $query,
        array $filters
    ): void {

        if (
            ! empty(
                $filters['id_sucursal']
            )
        ) {

            $query->where(

                'compra.id_sucursal',

                $filters['id_sucursal']

            );

        }

        if (
            ! empty(
                $filters['id_proveedor']
            )
        ) {

            $query->where(

                'compra.id_proveedor',

                $filters['id_proveedor']

            );

        }

        if (
            ! empty(
                $filters['id_usuario_comprador']
            )
        ) {

            $query->where(

                'compra.id_usuario_comprador',

                $filters[
                    'id_usuario_comprador'
                ]

            );

        }

        if (
            ! empty(
                $filters['id_producto_variante']
            )
        ) {

            $query->where(

                'compra_detalle.id_producto_variante',

                $filters[
                    'id_producto_variante'
                ]

            );

        }

        if (
            ! empty(
                $filters['fecha_desde']
            )
        ) {

            $query->whereDate(

                'compra.fecha_compra',

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

                'compra.fecha_compra',

                '<=',

                $filters['fecha_hasta']

            );

        }
    }
}