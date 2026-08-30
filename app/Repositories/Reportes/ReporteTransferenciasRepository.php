<?php

namespace App\Repositories\Reportes;

use App\Models\Transferencias\TransferenciaInventario;
use App\Models\Transferencias\TransferenciaInventarioDetalle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ReporteTransferenciasRepository
{
    /**
     * Obtiene transferencias aplicando filtros.
     */
    public function getTransferencias(
        array $filters
    ): Collection {

        $query =
            TransferenciaInventario::with([

                'sucursalOrigen',

                'sucursalDestino',

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
                'fecha_solicitud'
            )
            ->orderByDesc(
                'id_transferencia_inventario'
            )
            ->get();
    }

    /**
     * Obtiene productos más transferidos.
     *
     * Solo considera transferencias completadas.
     */
    public function getProductosMasTransferidos(
        array $filters
    ): Collection {

        $limite =
            $filters['limite'] ?? 10;

        $query =
            TransferenciaInventarioDetalle::query()
                ->join(
                    'transferencia_inventario',
                    'transferencia_inventario.id_transferencia_inventario',
                    '=',
                    'transferencia_inventario_detalle.id_transferencia_inventario'
                )
                ->where(
                    'transferencia_inventario.estado_registro',
                    'A'
                )
                ->where(
                    'transferencia_inventario_detalle.estado_registro',
                    'A'
                )
                ->where(
                    'transferencia_inventario.estado_transferencia',
                    TransferenciaInventario::COMPLETADA
                );

        $this->applyDetalleFilters(
            $query,
            $filters
        );

        return $query
            ->select(
                'transferencia_inventario_detalle.id_producto_variante'
            )
            ->selectRaw(
                'SUM(transferencia_inventario_detalle.cantidad) AS cantidad'
            )
            ->selectRaw(
                'COUNT(DISTINCT transferencia_inventario.id_transferencia_inventario) AS operaciones'
            )
            ->groupBy(
                'transferencia_inventario_detalle.id_producto_variante'
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
                $filters['id_sucursal_origen']
            )
        ) {

            $query->where(

                'id_sucursal_origen',

                $filters[
                    'id_sucursal_origen'
                ]

            );

        }

        if (
            ! empty(
                $filters['id_sucursal_destino']
            )
        ) {

            $query->where(

                'id_sucursal_destino',

                $filters[
                    'id_sucursal_destino'
                ]

            );

        }

        if (
            ! empty(
                $filters['estado_transferencia']
            )
        ) {

            $query->where(

                'estado_transferencia',

                $filters[
                    'estado_transferencia'
                ]

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

                'fecha_solicitud',

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

                'fecha_solicitud',

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
                $filters['id_sucursal_origen']
            )
        ) {

            $query->where(

                'transferencia_inventario.id_sucursal_origen',

                $filters[
                    'id_sucursal_origen'
                ]

            );

        }

        if (
            ! empty(
                $filters['id_sucursal_destino']
            )
        ) {

            $query->where(

                'transferencia_inventario.id_sucursal_destino',

                $filters[
                    'id_sucursal_destino'
                ]

            );

        }

        if (
            ! empty(
                $filters['id_producto_variante']
            )
        ) {

            $query->where(

                'transferencia_inventario_detalle.id_producto_variante',

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

                'transferencia_inventario.fecha_solicitud',

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

                'transferencia_inventario.fecha_solicitud',

                '<=',

                $filters['fecha_hasta']

            );

        }
    }
}