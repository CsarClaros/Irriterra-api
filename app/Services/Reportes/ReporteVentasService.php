<?php

namespace App\Services\Reportes;

use App\Models\Inventario\ProductoVariante;
use App\Models\Ventas\Venta;
use App\Repositories\Reportes\ReporteVentasRepository;

class ReporteVentasService
{
    public function __construct(
        private readonly ReporteVentasRepository $repository
    ) {
    }

    /**
     * Resumen y listado de ventas.
     */
    public function resumen(
        array $filters
    ): array {

        $ventas =
            $this->repository->getVentas(
                $filters
            );

        $completadas =
            $ventas->where(
                'estado_venta',
                Venta::COMPLETADA
            );

        $totalNeto =
            round(
                $completadas->sum('total'),
                2
            );

        return [

            'resumen' => [

                'ventas' =>
                    $ventas->count(),

                'borradores' =>
                    $ventas
                        ->where(
                            'estado_venta',
                            Venta::BORRADOR
                        )
                        ->count(),

                'completadas' =>
                    $completadas->count(),

                'anuladas' =>
                    $ventas
                        ->where(
                            'estado_venta',
                            Venta::ANULADA
                        )
                        ->count(),

                'subtotal_completado' =>
                    round(
                        $completadas->sum(
                            'subtotal'
                        ),
                        2
                    ),

                'descuento_completado' =>
                    round(
                        $completadas->sum(
                            'descuento'
                        ),
                        2
                    ),

                'total_vendido' =>
                    $totalNeto,

                'ticket_promedio' =>
                    $completadas->count() > 0

                        ? round(
                            $totalNeto
                            / $completadas->count(),
                            2
                        )

                        : 0

            ],

            'data' =>
                $ventas

        ];
    }

    /**
     * Productos más vendidos.
     */
    public function productos(
        array $filters
    ): array {

        $registros =
            $this->repository
                ->getProductosMasVendidos(
                    $filters
                );

        $variantes =
            ProductoVariante::with([
                'producto.categoria'
            ])
                ->whereIn(
                    'id_producto_variante',
                    $registros->pluck(
                        'id_producto_variante'
                    )
                )
                ->get()
                ->keyBy(
                    'id_producto_variante'
                );

        $data =
            $registros->map(
                fn ($registro) => [

                    'id_producto_variante' =>
                        $registro
                            ->id_producto_variante,

                    'cantidad' =>
                        round(
                            (float)
                            $registro->cantidad,
                            3
                        ),

                    'total' =>
                        round(
                            (float)
                            $registro->total,
                            2
                        ),

                    'operaciones' =>
                        (int)
                        $registro->operaciones,

                    'producto_variante' =>
                        $variantes->get(
                            $registro
                                ->id_producto_variante
                        )

                ]
            );

        return [

            'resumen' => [

                'productos' =>
                    $data->count(),

                'cantidad_total' =>
                    round(
                        $data->sum('cantidad'),
                        3
                    ),

                'importe_total' =>
                    round(
                        $data->sum('total'),
                        2
                    )

            ],

            'data' =>
                $data

        ];
    }
}