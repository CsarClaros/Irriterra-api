<?php

namespace App\Services\Reportes;

use App\Repositories\Reportes\ReporteInventarioRepository;
use Illuminate\Support\Collection;

class ReporteInventarioService
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly ReporteInventarioRepository $repository
    ) {
    }

    /**
     * Reporte general de stock.
     */
    public function stock(
        array $filters
    ): array {

        $stocks =
            $this->repository->getStock(
                $filters
            );

        $data =
            $stocks->map(

                fn ($stock) => [

                    'id_stock_sucursal' =>
                        $stock->id_stock_sucursal,

                    'id_sucursal' =>
                        $stock->id_sucursal,

                    'id_producto_variante' =>
                        $stock->id_producto_variante,

                    'stock_actual' =>
                        (float)
                        $stock->stock_actual,

                    'stock_minimo' =>
                        (float)
                        $stock->stock_minimo,

                    'stock_maximo' =>
                        $stock->stock_maximo !== null

                            ? (float)
                                $stock->stock_maximo

                            : null,

                    'estado_stock' =>
                        $this->determinarEstadoStock(

                            $stock->stock_actual,

                            $stock->stock_minimo

                        ),

                    'ubicacion_almacen' =>
                        $stock->ubicacion_almacen,

                    'sucursal' =>
                        $stock->sucursal,

                    'producto_variante' =>
                        $stock->productoVariante

                ]

            );

        return [

            'resumen' => [

                'registros' =>
                    $data->count(),

                'agotados' =>
                    $data
                        ->where(
                            'estado_stock',
                            'AGOTADO'
                        )
                        ->count(),

                'bajo_minimo' =>
                    $data
                        ->where(
                            'estado_stock',
                            'BAJO'
                        )
                        ->count(),

                'normales' =>
                    $data
                        ->where(
                            'estado_stock',
                            'NORMAL'
                        )
                        ->count(),

                'stock_total' =>
                    round(

                        $data->sum(
                            'stock_actual'
                        ),

                        3

                    )

            ],

            'data' => $data

        ];
    }

    /**
     * Reporte exclusivo de stock bajo o agotado.
     */
    public function stockBajoMinimo(
        array $filters
    ): array {

        unset(
            $filters['estado_stock']
        );

        $stocks =
            $this->repository->getStock(
                $filters
            );

        $data =
            $stocks
                ->filter(
                    function ($stock): bool {

                        return
                            (float)
                            $stock->stock_actual
                            <=
                            (float)
                            $stock->stock_minimo;

                    }
                )
                ->map(

                    fn ($stock) => [

                        'id_stock_sucursal' =>
                            $stock
                                ->id_stock_sucursal,

                        'id_sucursal' =>
                            $stock->id_sucursal,

                        'id_producto_variante' =>
                            $stock
                                ->id_producto_variante,

                        'stock_actual' =>
                            (float)
                            $stock->stock_actual,

                        'stock_minimo' =>
                            (float)
                            $stock->stock_minimo,

                        'stock_maximo' =>
                            $stock->stock_maximo !== null

                                ? (float)
                                    $stock->stock_maximo

                                : null,

                        'estado_stock' =>
                            $this->determinarEstadoStock(

                                $stock->stock_actual,

                                $stock->stock_minimo

                            ),

                        'faltante_minimo' =>
                            round(

                                max(

                                    0,

                                    (float)
                                    $stock->stock_minimo

                                    -

                                    (float)
                                    $stock->stock_actual

                                ),

                                3

                            ),

                        'ubicacion_almacen' =>
                            $stock
                                ->ubicacion_almacen,

                        'sucursal' =>
                            $stock->sucursal,

                        'producto_variante' =>
                            $stock->productoVariante

                    ]

                )
                ->values();

        return [

            'resumen' => [

                'registros' =>
                    $data->count(),

                'agotados' =>
                    $data
                        ->where(
                            'estado_stock',
                            'AGOTADO'
                        )
                        ->count(),

                'bajo_minimo' =>
                    $data
                        ->where(
                            'estado_stock',
                            'BAJO'
                        )
                        ->count(),

                'faltante_total' =>
                    round(

                        $data->sum(
                            'faltante_minimo'
                        ),

                        3

                    )

            ],

            'data' => $data

        ];
    }

    /**
     * Reporte de valoración del inventario.
     */
    public function valoracion(
        array $filters
    ): array {

        $stocks =
            $this->repository->getStock(
                $filters
            );

        $idsProductoVariante =
            $stocks
                ->pluck(
                    'id_producto_variante'
                )
                ->unique()
                ->values()
                ->all();

        $precios =
            $this->repository
                ->getPreciosByVariantes(

                    $idsProductoVariante

                );

        $data =
            $stocks->map(
                function ($stock) use (
                    $precios
                ): array {

                    $precio =
                        $precios->get(

                            $stock
                                ->id_producto_variante

                        );

                    $costoCompra =
                        $precio

                            ? (float)
                                $precio->costo_compra

                            : 0;

                    $valorCosto = round(

                        (float)
                        $stock->stock_actual

                        *

                        $costoCompra,

                        2

                    );

                    return [

                        'id_stock_sucursal' =>
                            $stock
                                ->id_stock_sucursal,

                        'id_sucursal' =>
                            $stock->id_sucursal,

                        'id_producto_variante' =>
                            $stock
                                ->id_producto_variante,

                        'stock_actual' =>
                            (float)
                            $stock->stock_actual,

                        'costo_compra' =>
                            round(
                                $costoCompra,
                                2
                            ),

                        'valor_inventario' =>
                            $valorCosto,

                        'tiene_precio_configurado' =>
                            $precio !== null,

                        'sucursal' =>
                            $stock->sucursal,

                        'producto_variante' =>
                            $stock->productoVariante

                    ];

                }
            );

        return [

            'resumen' => [

                'registros' =>
                    $data->count(),

                'stock_total' =>
                    round(

                        $data->sum(
                            'stock_actual'
                        ),

                        3

                    ),

                'valor_total_inventario' =>
                    round(

                        $data->sum(
                            'valor_inventario'
                        ),

                        2

                    ),

                'sin_precio_configurado' =>
                    $data
                        ->where(
                            'tiene_precio_configurado',
                            false
                        )
                        ->count()

            ],

            'data' => $data

        ];
    }

    /**
     * Reporte de movimientos o kardex.
     */
    public function kardex(
        array $filters
    ): array {

        $movimientos =
            $this->repository->getMovimientos(
                $filters
            );

        $data =
            $movimientos->map(

                fn ($movimiento) => [

                    'id_movimiento_inventario' =>
                        $movimiento
                            ->id_movimiento_inventario,

                    'codigo_movimiento' =>
                        $movimiento
                            ->codigo_movimiento,

                    'id_stock_sucursal' =>
                        $movimiento
                            ->id_stock_sucursal,

                    'tipo_movimiento' =>
                        $movimiento
                            ->tipo_movimiento,

                    'cantidad' =>
                        (float)
                        $movimiento->cantidad,

                    'stock_anterior' =>
                        (float)
                        $movimiento
                            ->stock_anterior,

                    'stock_resultante' =>
                        (float)
                        $movimiento
                            ->stock_resultante,

                    'motivo' =>
                        $movimiento->motivo,

                    'tipo_referencia' =>
                        $movimiento
                            ->tipo_referencia,

                    'id_referencia' =>
                        $movimiento
                            ->id_referencia,

                    'fecha_movimiento' =>
                        $movimiento
                            ->fecha_movimiento,

                    'observaciones' =>
                        $movimiento
                            ->observaciones,

                    'stock_sucursal' =>
                        $movimiento
                            ->stockSucursal

                ]

            );

        return [

            'resumen' => [

                'movimientos' =>
                    $data->count(),

                'cantidad_entradas' =>
                    $this->sumarCantidadPorTipo(

                        $data,

                        [
                            'ENTRADA',
                            'AJUSTE_ENTRADA'
                        ]

                    ),

                'cantidad_salidas' =>
                    $this->sumarCantidadPorTipo(

                        $data,

                        [
                            'SALIDA',
                            'AJUSTE_SALIDA'
                        ]

                    )

            ],

            'data' => $data

        ];
    }

    /**
     * Determina el estado del stock.
     */
    private function determinarEstadoStock(
        float|int|string $stockActual,
        float|int|string $stockMinimo
    ): string {

        $actual =
            (float) $stockActual;

        $minimo =
            (float) $stockMinimo;

        if ($actual <= 0) {

            return 'AGOTADO';

        }

        if ($actual <= $minimo) {

            return 'BAJO';

        }

        return 'NORMAL';
    }

    /**
     * Suma cantidades para varios tipos.
     */
    private function sumarCantidadPorTipo(
        Collection $movimientos,
        array $tipos
    ): float {

        return round(

            $movimientos
                ->whereIn(
                    'tipo_movimiento',
                    $tipos
                )
                ->sum(
                    'cantidad'
                ),

            3

        );
    }
}