<?php

namespace App\Services\Reportes;

use App\Models\Inventario\ProductoVariante;
use App\Models\Transferencias\TransferenciaInventario;
use App\Repositories\Reportes\ReporteTransferenciasRepository;

class ReporteTransferenciasService
{
    public function __construct(
        private readonly ReporteTransferenciasRepository $repository
    ) {
    }

    /**
     * Resumen y listado de transferencias.
     */
    public function resumen(
        array $filters
    ): array {

        $transferencias =
            $this->repository
                ->getTransferencias(
                    $filters
                );

        $cantidadTotal =
            $transferencias->sum(

                fn ($transferencia) =>
                    $transferencia
                        ->detalles
                        ->sum('cantidad')

            );

        $completadas =
            $transferencias->where(
                'estado_transferencia',
                TransferenciaInventario::COMPLETADA
            );

        return [

            'resumen' => [

                'transferencias' =>
                    $transferencias->count(),

                'pendientes' =>
                    $transferencias
                        ->where(
                            'estado_transferencia',
                            TransferenciaInventario::PENDIENTE
                        )
                        ->count(),

                'en_transito' =>
                    $transferencias
                        ->where(
                            'estado_transferencia',
                            TransferenciaInventario::EN_TRANSITO
                        )
                        ->count(),

                'completadas' =>
                    $completadas->count(),

                'rechazadas' =>
                    $transferencias
                        ->where(
                            'estado_transferencia',
                            TransferenciaInventario::RECHAZADA
                        )
                        ->count(),

                'cantidad_total_solicitada' =>
                    round(
                        (float)
                        $cantidadTotal,
                        3
                    ),

                'cantidad_completada' =>
                    round(

                        (float)
                        $completadas->sum(

                            fn ($transferencia) =>
                                $transferencia
                                    ->detalles
                                    ->sum('cantidad')

                        ),

                        3

                    )

            ],

            'data' =>
                $transferencias

        ];
    }

    /**
     * Productos más transferidos.
     */
    public function productos(
        array $filters
    ): array {

        $registros =
            $this->repository
                ->getProductosMasTransferidos(
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
                    )

            ],

            'data' =>
                $data

        ];
    }
}