<?php

namespace App\Services\Reportes;

use App\Models\Compras\Compra;
use App\Models\Inventario\ProductoVariante;
use App\Repositories\Reportes\ReporteComprasRepository;

class ReporteComprasService
{
    public function __construct(
        private readonly ReporteComprasRepository $repository
    ) {
    }

    /**
     * Resumen y listado de compras.
     */
    public function resumen(
        array $filters
    ): array {

        $compras =
            $this->repository->getCompras(
                $filters
            );

        $recibidas =
            $compras->where(
                'estado_compra',
                Compra::RECIBIDA
            );

        $totalRecibido =
            round(
                $recibidas->sum('total'),
                2
            );

        return [

            'resumen' => [

                'compras' =>
                    $compras->count(),

                'borradores' =>
                    $compras
                        ->where(
                            'estado_compra',
                            Compra::BORRADOR
                        )
                        ->count(),

                'confirmadas' =>
                    $compras
                        ->where(
                            'estado_compra',
                            Compra::CONFIRMADA
                        )
                        ->count(),

                'recibidas' =>
                    $recibidas->count(),

                'anuladas' =>
                    $compras
                        ->where(
                            'estado_compra',
                            Compra::ANULADA
                        )
                        ->count(),

                'subtotal_recibido' =>
                    round(
                        $recibidas->sum(
                            'subtotal'
                        ),
                        2
                    ),

                'descuento_recibido' =>
                    round(
                        $recibidas->sum(
                            'descuento'
                        ),
                        2
                    ),

                'total_comprado' =>
                    $totalRecibido,

                'compra_promedio' =>
                    $recibidas->count() > 0

                        ? round(
                            $totalRecibido
                            / $recibidas->count(),
                            2
                        )

                        : 0

            ],

            'data' =>
                $compras

        ];
    }

    /**
     * Productos más comprados.
     */
    public function productos(
        array $filters
    ): array {

        $registros =
            $this->repository
                ->getProductosMasComprados(
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