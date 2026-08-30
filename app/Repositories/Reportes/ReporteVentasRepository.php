<?php

namespace App\Repositories\Reportes;

use App\Models\Ventas\Venta;
use App\Models\Ventas\VentaDetalle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ReporteVentasRepository
{
    /**
     * Obtiene ventas aplicando filtros.
     */
    public function getVentas(
        array $filters
    ): Collection {

        $query =
            Venta::with([

                'sucursal',

                'cliente',

                'vendedor',

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
                'fecha_venta'
            )
            ->orderByDesc(
                'id_venta'
            )
            ->get();
    }

    /**
     * Obtiene productos más vendidos.
     *
     * Solo considera ventas completadas.
     */
    public function getProductosMasVendidos(
        array $filters
    ): Collection {

        $limite =
            $filters['limite'] ?? 10;

        $query =
            VentaDetalle::query()
                ->join(
                    'venta',
                    'venta.id_venta',
                    '=',
                    'venta_detalle.id_venta'
                )
                ->where(
                    'venta.estado_registro',
                    'A'
                )
                ->where(
                    'venta_detalle.estado_registro',
                    'A'
                )
                ->where(
                    'venta.estado_venta',
                    Venta::COMPLETADA
                );

        $this->applyDetalleFilters(
            $query,
            $filters
        );

        return $query
            ->select(
                'venta_detalle.id_producto_variante'
            )
            ->selectRaw(
                'SUM(venta_detalle.cantidad) AS cantidad'
            )
            ->selectRaw(
                'SUM(venta_detalle.subtotal) AS total'
            )
            ->selectRaw(
                'COUNT(DISTINCT venta.id_venta) AS operaciones'
            )
            ->groupBy(
                'venta_detalle.id_producto_variante'
            )
            ->orderByDesc(
                'cantidad'
            )
            ->limit(
                $limite
            )
            ->get();
    }

    /**
     * Aplica filtros a la cabecera.
     */
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
                $filters['id_cliente']
            )
        ) {

            $query->where(

                'id_cliente',

                $filters['id_cliente']

            );

        }

        if (
            ! empty(
                $filters['id_usuario_vendedor']
            )
        ) {

            $query->where(

                'id_usuario_vendedor',

                $filters[
                    'id_usuario_vendedor'
                ]

            );

        }

        if (
            ! empty(
                $filters['estado_venta']
            )
        ) {

            $query->where(

                'estado_venta',

                $filters['estado_venta']

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

                'fecha_venta',

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

                'fecha_venta',

                '<=',

                $filters['fecha_hasta']

            );

        }
    }

    /**
     * Aplica filtros al agregado por producto.
     */
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

                'venta.id_sucursal',

                $filters['id_sucursal']

            );

        }

        if (
            ! empty(
                $filters['id_cliente']
            )
        ) {

            $query->where(

                'venta.id_cliente',

                $filters['id_cliente']

            );

        }

        if (
            ! empty(
                $filters['id_usuario_vendedor']
            )
        ) {

            $query->where(

                'venta.id_usuario_vendedor',

                $filters[
                    'id_usuario_vendedor'
                ]

            );

        }

        if (
            ! empty(
                $filters['id_producto_variante']
            )
        ) {

            $query->where(

                'venta_detalle.id_producto_variante',

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

                'venta.fecha_venta',

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

                'venta.fecha_venta',

                '<=',

                $filters['fecha_hasta']

            );

        }
    }
}