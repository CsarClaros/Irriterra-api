<?php

namespace App\Services\Ventas;

use App\Models\Inventario\MovimientoInventario;
use App\Models\Inventario\PrecioProductoVariante;
use App\Models\Inventario\StockSucursal;
use App\Models\Ventas\Venta;
use App\Repositories\Ventas\VentaRepository;
use App\Services\Inventario\MovimientoInventarioService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class VentaService
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly VentaRepository $repository,
        private readonly MovimientoInventarioService $movimientoService
    ) {
    }

    /**
     * Lista ventas activas.
     */
    public function index(): Collection
    {
        return $this->repository->getAll();
    }

    /**
     * Muestra una venta activa.
     */
    public function show(
        int $id
    ): Venta {

        return $this->repository->findById(
            $id
        );
    }

    /**
     * Registra una venta en borrador.
     */
    public function store(
        array $data
    ): Venta {

        return DB::transaction(
            function () use ($data) {

                $detallesEntrada =
                    $data['detalles'];

                unset($data['detalles']);

                $detalles =
                    $this->prepararDetalles(
                        $detallesEntrada
                    );

                $totales =
                    $this->calcularTotales(
                        $detalles
                    );

                $data['codigo_venta'] =
                    'VEN-' . Str::upper(
                        (string) Str::ulid()
                    );

                $data['estado_venta'] =
                    Venta::BORRADOR;

                $data['fecha_venta'] =
                    $data['fecha_venta']
                        ?? now();

                $data['subtotal'] =
                    $totales['subtotal'];

                $data['descuento'] =
                    $totales['descuento'];

                $data['total'] =
                    $totales['total'];

                $data['monto_pagado'] = 0;

                $data['metodo_pago'] = null;

                $data['fecha_pago'] = null;

                $data['fecha_anulacion'] = null;

                $data['motivo_anulacion'] = null;

                $data['id_usuario_anulacion'] = null;

                $data['estado_registro'] = 'A';

                return $this->repository->create(

                    $data,

                    $detalles

                );

            }
        );
    }

    /**
     * Actualiza una venta en borrador.
     */
    public function update(
        Venta $venta,
        array $data
    ): Venta {

        return DB::transaction(
            function () use (
                $venta,
                $data
            ) {

                $venta =
                    $this->repository
                        ->findByIdForUpdate(
                            $venta->id_venta
                        );

                $this->validarVentaBorrador(
                    $venta
                );

                $detalles = null;

                if (
                    array_key_exists(
                        'detalles',
                        $data
                    )
                ) {

                    $detallesEntrada =
                        $data['detalles'];

                    unset($data['detalles']);

                    $detalles =
                        $this->prepararDetalles(
                            $detallesEntrada
                        );

                    $totales =
                        $this->calcularTotales(
                            $detalles
                        );

                    $data['subtotal'] =
                        $totales['subtotal'];

                    $data['descuento'] =
                        $totales['descuento'];

                    $data['total'] =
                        $totales['total'];

                }

                unset(

                    $data['codigo_venta'],

                    $data['estado_venta'],

                    $data['monto_pagado'],

                    $data['metodo_pago'],

                    $data['fecha_pago'],

                    $data['fecha_anulacion'],

                    $data['motivo_anulacion'],

                    $data['id_usuario_anulacion'],

                    $data['estado_registro'],

                    $data['usuario_creacion']

                );

                return $this->repository->update(

                    $venta,

                    $data,

                    $detalles

                );

            }
        );
    }

    /**
     * Completa una venta, registra el pago
     * y descuenta el stock.
     */
    public function completar(
        Venta $venta,
        array $data
    ): Venta {

        return DB::transaction(
            function () use (
                $venta,
                $data
            ) {

                $venta =
                    $this->repository
                        ->findByIdForUpdate(
                            $venta->id_venta
                        );

                $this->validarVentaBorrador(
                    $venta
                );

                $this->validarDetalles(
                    $venta
                );

                $this->validarMontoPagado(
                    $venta,
                    $data['monto_pagado']
                );

                /*
                 * Se ordenan las variantes para mantener
                 * un orden consistente de bloqueo.
                 */
                $detalles =
                    $venta->detalles
                        ->sortBy(
                            'id_producto_variante'
                        );

                $stocks = [];

                /*
                 * Antes de generar movimientos se validan
                 * todos los precios y todas las existencias.
                 */
                foreach (
                    $detalles as $detalle
                ) {

                    $this->validarPrecioMinimoActual(
                        $detalle
                    );

                    $stockSucursal =
                        $this->repository
                            ->findStockBySucursalVarianteForUpdate(

                                $venta->id_sucursal,

                                $detalle
                                    ->id_producto_variante

                            );

                    if (! $stockSucursal) {

                        throw ValidationException::withMessages([

                            'detalles' => [

                                'Una variante no posee stock activo en la sucursal de la venta.'

                            ]

                        ]);

                    }

                    if (
                        (float) $detalle->cantidad
                        > (float) $stockSucursal
                            ->stock_actual
                    ) {

                        throw ValidationException::withMessages([

                            'cantidad' => [

                                'La cantidad solicitada supera el stock disponible.'

                            ]

                        ]);

                    }

                    $stocks[
                        $detalle->id_producto_variante
                    ] = $stockSucursal;

                }

                /*
                 * Después de validar todos los registros,
                 * se generan las salidas.
                 */
                foreach (
                    $detalles as $detalle
                ) {

                    /** @var StockSucursal $stockSucursal */
                    $stockSucursal =
                        $stocks[
                            $detalle
                                ->id_producto_variante
                        ];

                    $this->movimientoService->store([

                        'id_stock_sucursal' =>
                            $stockSucursal
                                ->id_stock_sucursal,

                        'tipo_movimiento' =>
                            MovimientoInventario::SALIDA,

                        'cantidad' =>
                            $detalle->cantidad,

                        'motivo' =>
                            'Salida por venta '
                            . $venta->codigo_venta,

                        'tipo_referencia' =>
                            'VENTA',

                        'id_referencia' =>
                            $venta->id_venta,

                        'observaciones' =>
                            'Salida automática por venta completada.',

                        'usuario_creacion' =>
                            $data[
                                'usuario_modificacion'
                            ]

                    ]);

                }

                return $this->repository->updateState(

                    $venta,

                    [
                        'estado_venta' =>
                            Venta::COMPLETADA,

                        'monto_pagado' =>
                            round(
                                (float)
                                $data['monto_pagado'],
                                2
                            ),

                        'metodo_pago' =>
                            $data['metodo_pago'],

                        'fecha_pago' =>
                            now(),

                        'usuario_modificacion' =>
                            $data[
                                'usuario_modificacion'
                            ]
                    ]

                );

            }
        );
    }

    /**
     * Anula una venta completada y devuelve
     * las existencias a la sucursal.
     */
    public function anular(
        Venta $venta,
        array $data
    ): Venta {

        return DB::transaction(
            function () use (
                $venta,
                $data
            ) {

                $venta =
                    $this->repository
                        ->findByIdForUpdate(
                            $venta->id_venta
                        );

                $this->validarVentaCompletada(
                    $venta
                );

                $this->validarDetalles(
                    $venta
                );

                $detalles =
                    $venta->detalles
                        ->sortBy(
                            'id_producto_variante'
                        );

                $stocks = [];

                /*
                 * Primero se bloquean y validan todos
                 * los stocks relacionados.
                 */
                foreach (
                    $detalles as $detalle
                ) {

                    $stockSucursal =
                        $this->repository
                            ->findStockBySucursalVarianteForUpdate(

                                $venta->id_sucursal,

                                $detalle
                                    ->id_producto_variante

                            );

                    if (! $stockSucursal) {

                        throw ValidationException::withMessages([

                            'detalles' => [

                                'No se encontró el stock de la sucursal para devolver la venta.'

                            ]

                        ]);

                    }

                    $stocks[
                        $detalle->id_producto_variante
                    ] = $stockSucursal;

                }

                /*
                 * Se generan entradas compensatorias.
                 */
                foreach (
                    $detalles as $detalle
                ) {

                    /** @var StockSucursal $stockSucursal */
                    $stockSucursal =
                        $stocks[
                            $detalle
                                ->id_producto_variante
                        ];

                    $this->movimientoService->store([

                        'id_stock_sucursal' =>
                            $stockSucursal
                                ->id_stock_sucursal,

                        'tipo_movimiento' =>
                            MovimientoInventario::ENTRADA,

                        'cantidad' =>
                            $detalle->cantidad,

                        'motivo' =>
                            'Anulación de venta '
                            . $venta->codigo_venta,

                        'tipo_referencia' =>
                            'VENTA',

                        'id_referencia' =>
                            $venta->id_venta,

                        'observaciones' =>
                            'Devolución automática por anulación de venta.',

                        'usuario_creacion' =>
                            $data[
                                'id_usuario_anulacion'
                            ]

                    ]);

                }

                return $this->repository->updateState(

                    $venta,

                    [
                        'estado_venta' =>
                            Venta::ANULADA,

                        'fecha_anulacion' =>
                            now(),

                        'motivo_anulacion' =>
                            $data[
                                'motivo_anulacion'
                            ],

                        'id_usuario_anulacion' =>
                            $data[
                                'id_usuario_anulacion'
                            ],

                        'usuario_modificacion' =>
                            $data[
                                'id_usuario_anulacion'
                            ]
                    ]

                );

            }
        );
    }

    /**
     * Elimina lógicamente una venta en borrador.
     */
    public function destroy(
        Venta $venta
    ): bool {

        return DB::transaction(
            function () use ($venta) {

                $venta =
                    $this->repository
                        ->findByIdForUpdate(
                            $venta->id_venta
                        );

                $this->validarVentaBorrador(
                    $venta
                );

                return $this->repository->delete(
                    $venta
                );

            }
        );
    }

    /**
     * Prepara y calcula los detalles.
     */
    private function prepararDetalles(
        array $detallesEntrada
    ): array {

        $detalles = [];

        foreach (
            $detallesEntrada
            as $indice => $detalleEntrada
        ) {

            $precioProductoVariante =
                PrecioProductoVariante::where(
                    'id_producto_variante',
                    $detalleEntrada[
                        'id_producto_variante'
                    ]
                )
                    ->where(
                        'estado_registro',
                        'A'
                    )
                    ->first();

            if (! $precioProductoVariante) {

                throw ValidationException::withMessages([

                    "detalles.{$indice}.id_producto_variante" => [

                        'La variante no tiene una configuración de precios activa.'

                    ]

                ]);

            }

            $cantidad =
                (float) $detalleEntrada[
                    'cantidad'
                ];

            $precioMinimo =
                (float) $precioProductoVariante
                    ->precio_minimo;

            $precioVenta =
                (float) $precioProductoVariante
                    ->precio_venta;

            $precioUnitario =
                array_key_exists(
                    'precio_unitario',
                    $detalleEntrada
                )
                && $detalleEntrada[
                    'precio_unitario'
                ] !== null

                    ? (float) $detalleEntrada[
                        'precio_unitario'
                    ]

                    : $precioVenta;

            if (
                $precioUnitario
                < $precioMinimo
            ) {

                throw ValidationException::withMessages([

                    "detalles.{$indice}.precio_unitario" => [

                        'El precio unitario no puede ser menor al precio mínimo permitido.'

                    ]

                ]);

            }

            $descuento =
                isset(
                    $detalleEntrada['descuento']
                )
                    ? (float)
                        $detalleEntrada[
                            'descuento'
                        ]
                    : 0;

            $importeBruto = round(

                $cantidad * $precioUnitario,

                2

            );

            if (
                $descuento
                > $importeBruto
            ) {

                throw ValidationException::withMessages([

                    "detalles.{$indice}.descuento" => [

                        'El descuento no puede superar el importe bruto del detalle.'

                    ]

                ]);

            }

            $subtotalDetalle = round(

                $importeBruto - $descuento,

                2

            );

            /*
|--------------------------------------------------------------------------
| Precio efectivo mínimo
|--------------------------------------------------------------------------
|
| El descuento no puede provocar que el importe
| efectivo quede por debajo del precio mínimo.
|
*/

            $importeMinimoPermitido = round(

                $cantidad * $precioMinimo,

                2

            );


            if (
                $subtotalDetalle
                < $importeMinimoPermitido
            ) {

                throw ValidationException::withMessages([

                    "detalles.{$indice}.descuento" => [

                        'El descuento hace que el precio efectivo quede por debajo del precio mínimo permitido.'

                    ]

                ]);

            }

            $detalles[] = [

                'id_producto_variante' =>
                    $detalleEntrada[
                        'id_producto_variante'
                    ],

                'cantidad' =>
                    $cantidad,

                'precio_unitario' =>
                    round(
                        $precioUnitario,
                        2
                    ),

                'costo_unitario' =>
                    round(
                        (float)
                        $precioProductoVariante
                            ->costo_compra,
                        2
                    ),

                'descuento' =>
                    round(
                        $descuento,
                        2
                    ),

                'subtotal' =>
                    $subtotalDetalle,

                'importe_bruto' =>
                    $importeBruto,

                'observaciones' =>
                    $detalleEntrada[
                        'observaciones'
                    ] ?? null

            ];

        }

        return $detalles;
    }

    /**
     * Calcula los totales generales.
     */
    private function calcularTotales(
        array $detalles
    ): array {

        $subtotal = round(

            collect($detalles)->sum(

                fn (array $detalle) =>
                    $detalle['importe_bruto']

            ),

            2

        );

        $descuento = round(

            collect($detalles)->sum(

                fn (array $detalle) =>
                    $detalle['descuento']

            ),

            2

        );

        $total = round(

            $subtotal - $descuento,

            2

        );

        return [

            'subtotal' =>
                $subtotal,

            'descuento' =>
                $descuento,

            'total' =>
                $total

        ];
    }

    /**
     * Verifica que la venta esté en borrador.
     */
    private function validarVentaBorrador(
        Venta $venta
    ): void {

        if (
            $venta->estado_venta
            !== Venta::BORRADOR
        ) {

            throw ValidationException::withMessages([

                'estado_venta' => [

                    'Solo pueden modificarse, eliminarse o completarse ventas en borrador.'

                ]

            ]);

        }
    }

    /**
     * Verifica que la venta esté completada.
     */
    private function validarVentaCompletada(
        Venta $venta
    ): void {

        if (
            $venta->estado_venta
            !== Venta::COMPLETADA
        ) {

            throw ValidationException::withMessages([

                'estado_venta' => [

                    'Solo pueden anularse ventas completadas.'

                ]

            ]);

        }
    }

    /**
     * Verifica que existan detalles activos.
     */
    private function validarDetalles(
        Venta $venta
    ): void {

        if (
            $venta->detalles->isEmpty()
        ) {

            throw ValidationException::withMessages([

                'detalles' => [

                    'La venta debe contener al menos un detalle activo.'

                ]

            ]);

        }

        if (
            $this->convertirACentavos(
                $venta->total
            ) <= 0
        ) {

            throw ValidationException::withMessages([

                'total' => [

                    'El total de la venta debe ser mayor que cero.'

                ]

            ]);

        }
    }

    /**
     * Valida que el monto pagado sea exactamente
     * igual al total de la venta.
     */
    private function validarMontoPagado(
        Venta $venta,
        float|int|string $montoPagado
    ): void {

        $totalCentavos =
            $this->convertirACentavos(
                $venta->total
            );

        $montoCentavos =
            $this->convertirACentavos(
                $montoPagado
            );

        if (
            $totalCentavos
            !== $montoCentavos
        ) {

            throw ValidationException::withMessages([

                'monto_pagado' => [

                    'El monto pagado debe ser exactamente igual al total de la venta.'

                ]

            ]);

        }
    }

    /**
     * Revalida el precio mínimo vigente antes
     * de completar la venta.
     */
    private function validarPrecioMinimoActual(
        $detalle
    ): void {

        $precioProductoVariante =
            PrecioProductoVariante::where(
                'id_producto_variante',
                $detalle->id_producto_variante
            )
                ->where(
                    'estado_registro',
                    'A'
                )
                ->first();

        if (! $precioProductoVariante) {

            throw ValidationException::withMessages([

                'detalles' => [

                    'Una variante ya no tiene una configuración de precios activa.'

                ]

            ]);

        }

        $precioUnitarioCentavos =
            $this->convertirACentavos(
                $detalle->precio_unitario
            );

        $precioMinimoCentavos =
            $this->convertirACentavos(
                $precioProductoVariante
                    ->precio_minimo
            );

        if (
            $precioUnitarioCentavos
            < $precioMinimoCentavos
        ) {

            throw ValidationException::withMessages([

                'detalles' => [

                    'El precio de una variante es menor al precio mínimo vigente.'

                ]

            ]);

        }
    }

    /**
     * Convierte un importe decimal a centavos
     * para realizar comparaciones exactas.
     */
    private function convertirACentavos(
        float|int|string $importe
    ): int {

        return (int) round(
            (float) $importe * 100
        );
    }
}
