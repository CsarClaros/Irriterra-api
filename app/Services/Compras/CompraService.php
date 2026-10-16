<?php

namespace App\Services\Compras;

use App\Models\Compras\Compra;
use App\Repositories\Compras\CompraRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\Inventario\MovimientoInventario;
use App\Models\Inventario\StockSucursal;
use App\Services\Inventario\MovimientoInventarioService;
use App\Models\Inventario\ProductoVariante;
use App\Services\Inventario\InventarioItemService;

class CompraService
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly CompraRepository            $repository,
        private readonly MovimientoInventarioService $movimientoService,
        private readonly InventarioItemService       $inventarioItemService
    )
    {
    }

    /**
     * Lista las compras activas.
     */
    public function index(): Collection
    {
        return $this->repository->getAll();
    }

    /**
     * Muestra una compra activa.
     */
    public function show(
        int $id
    ): Compra
    {

        return $this->repository->findById(
            $id
        );
    }

    /**
     * Registra una compra en borrador.
     */
    public function store(
        array $data
    ): Compra
    {

        return DB::transaction(
            function () use ($data) {

                $detallesEntrada =
                    $data['detalles'];

                unset(
                    $data['detalles']
                );

                /*
                 * El backend determina todo el
                 * contexto monetario.
                 */
                $contextoMoneda =
                    $this->resolverContextoMoneda(
                        $data
                    );

                $data = array_merge(

                    $data,

                    $contextoMoneda['campos_compra']

                );

                $detalles =
                    $this->prepararDetalles(

                        $detallesEntrada,

                        $contextoMoneda['factor_conversion']

                    );

                $totales =
                    $this->calcularTotales(
                        $detalles
                    );

                $data['codigo_compra'] =
                    'COM-'
                    . Str::upper(
                        (string)Str::ulid()
                    );

                $data['estado_compra'] =
                    Compra::BORRADOR;

                $data['fecha_compra'] =
                    $data['fecha_compra']
                    ?? now();

                $data['fecha_confirmacion'] =
                    null;

                $data['fecha_recepcion'] =
                    null;

                $data['fecha_anulacion'] =
                    null;

                /*
                |--------------------------------------------------------------------------
                | Totales BOB
                |--------------------------------------------------------------------------
                */

                $data['subtotal'] =
                    $totales['subtotal'];

                $data['descuento'] =
                    $totales['descuento'];

                $data['total'] =
                    $totales['total'];

                /*
                |--------------------------------------------------------------------------
                | Totales moneda original
                |--------------------------------------------------------------------------
                */

                $data['subtotal_original'] =
                    $totales['subtotal_original'];

                $data['descuento_original'] =
                    $totales['descuento_original'];

                $data['total_original'] =
                    $totales['total_original'];

                $data['id_usuario_confirmacion'] =
                    null;

                $data['id_usuario_recepcion'] =
                    null;

                $data['id_usuario_anulacion'] =
                    null;

                $data['motivo_anulacion'] =
                    null;

                $data['estado_registro'] =
                    'A';

                return $this->repository
                    ->create(

                        $data,

                        $detalles

                    );
            }
        );
    }

    /**
     * Actualiza una compra en borrador.
     */
    public function update(
        Compra $compra,
        array  $data
    ): Compra
    {

        return DB::transaction(
            function () use (
                $compra,
                $data
            ) {

                $compra =
                    $this->repository
                        ->findByIdForUpdate(

                            $compra->id_compra

                        );

                $this->validarCompraBorrador(
                    $compra
                );

                /*
                 * Si llegan nuevos detalles usamos
                 * esos valores originales.
                 *
                 * Si no llegan, reconstruimos desde
                 * la compra actual. Esto permite cambiar
                 * únicamente el tipo de cambio y volver
                 * a calcular todos los importes BOB.
                 */
                if (
                    array_key_exists(
                        'detalles',
                        $data
                    )
                ) {

                    $detallesEntrada =
                        $data['detalles'];

                    unset(
                        $data['detalles']
                    );

                } else {

                    $detallesEntrada =
                        $compra
                            ->detalles
                            ->map(
                                function ($detalle) {

                                    return [

                                        'id_producto_variante' =>
                                            $detalle
                                                ->id_producto_variante,

                                        'cantidad' =>
                                            $detalle->cantidad,

                                        'costo_unitario_original' =>
                                            $detalle
                                                ->costo_unitario_original,

                                        'descuento_original' =>
                                            $detalle
                                                ->descuento_original,

                                        'observaciones' =>
                                            $detalle
                                                ->observaciones

                                    ];
                                }
                            )
                            ->values()
                            ->all();
                }

                /*
                 * Combinamos los datos nuevos con
                 * los valores monetarios actuales.
                 */
                $datosMoneda = [

                    'moneda' =>
                        $data['moneda']
                        ?? $compra->moneda,

                    'tipo_cambio_referencia' =>

                        array_key_exists(
                            'tipo_cambio_referencia',
                            $data
                        )

                            ? $data['tipo_cambio_referencia']

                            : $compra
                            ->tipo_cambio_referencia,

                    'tipo_cambio_aplicado' =>

                        array_key_exists(
                            'tipo_cambio_aplicado',
                            $data
                        )

                            ? $data['tipo_cambio_aplicado']

                            : $compra
                            ->tipo_cambio_aplicado

                ];

                $contextoMoneda =
                    $this->resolverContextoMoneda(

                        $datosMoneda,

                        $compra

                    );

                /*
                 * Campos que el cliente nunca puede
                 * controlar directamente.
                 */
                unset(

                    $data['codigo_compra'],

                    $data['estado_compra'],

                    $data['fecha_confirmacion'],

                    $data['fecha_recepcion'],

                    $data['fecha_anulacion'],

                    $data['subtotal'],

                    $data['descuento'],

                    $data['total'],

                    $data['subtotal_original'],

                    $data['descuento_original'],

                    $data['total_original'],

                    $data['origen_tipo_cambio'],

                    $data['fuente_tipo_cambio'],

                    $data['fecha_tipo_cambio'],

                    $data['id_usuario_confirmacion'],

                    $data['id_usuario_recepcion'],

                    $data['id_usuario_anulacion'],

                    $data['motivo_anulacion'],

                    $data['estado_registro'],

                    $data['usuario_creacion']

                );

                $data = array_merge(

                    $data,

                    $contextoMoneda['campos_compra']

                );

                $detalles =
                    $this->prepararDetalles(

                        $detallesEntrada,

                        $contextoMoneda['factor_conversion']

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

                $data['subtotal_original'] =
                    $totales['subtotal_original'];

                $data['descuento_original'] =
                    $totales['descuento_original'];

                $data['total_original'] =
                    $totales['total_original'];

                return $this->repository
                    ->update(

                        $compra,

                        $data,

                        $detalles

                    );
            }
        );
    }


    /**
     * Confirma una compra en borrador.
     *
     * Esta operación todavía no modifica el stock.
     */
    public function confirmar(
        Compra $compra,
        array  $data
    ): Compra
    {

        return DB::transaction(
            function () use (
                $compra,
                $data
            ) {

                $compra =
                    $this->repository
                        ->findByIdForUpdate(

                            $compra->id_compra

                        );

                $this->validarCompraBorrador(
                    $compra
                );

                $this->validarDetallesCompra(
                    $compra
                );

                return $this->repository->updateState(

                    $compra,

                    [
                        'estado_compra' =>
                            Compra::CONFIRMADA,

                        'fecha_confirmacion' =>
                            now(),

                        'id_usuario_confirmacion' =>
                            $data['id_usuario_confirmacion'],

                        'usuario_modificacion' =>
                            $data['id_usuario_confirmacion']
                    ]

                );
            }
        );
    }

    /**
     * Recibe una compra confirmada.
     *
     * Crea o reactiva stock, genera movimientos
     * de entrada y actualiza los costos.
     */
    public function recibir(
        Compra $compra,
        array  $data
    ): Compra
    {

        return DB::transaction(
            function () use (
                $compra,
                $data
            ) {

                $compra =
                    $this->repository
                        ->findByIdForUpdate(
                            $compra->id_compra
                        );

                $this->validarCompraConfirmada(
                    $compra
                );

                $this->validarDetallesCompra(
                    $compra
                );

                /*
                 * Mantiene un orden consistente de
                 * procesamiento y bloqueo.
                 */
                $detalles =
                    $compra->detalles
                        ->sortBy(
                            'id_producto_variante'
                        );

                $stocks = [];

                $precios = [];

                $costosNetos = [];

                /*
                |--------------------------------------------------------------------------
                | 1. Validar variantes, cantidades y precios
                |--------------------------------------------------------------------------
                */

                foreach (
                    $detalles as $detalle
                ) {

                    $productoVariante =
                        $detalle
                            ->productoVariante;

                    if (
                        !$productoVariante
                        ||
                        $productoVariante
                            ->estado_registro
                        !== 'A'
                    ) {

                        throw ValidationException::withMessages([

                            'detalles' => [

                                'Una variante de la compra ya no está disponible.'

                            ]

                        ]);
                    }

                    $tipoControl =
                        $productoVariante
                            ->tipo_control_inventario;

                    if (
                        !in_array(
                            $tipoControl,
                            ProductoVariante::
                            TIPOS_CONTROL_INVENTARIO,
                            true
                        )
                    ) {

                        throw ValidationException::withMessages([

                            'detalles' => [

                                'Una variante posee un tipo de control de inventario no válido.'

                            ]

                        ]);
                    }

                    $precioProductoVariante =
                        $this->repository
                            ->findPrecioProductoVarianteForUpdate(
                                $detalle
                                    ->id_producto_variante
                            );

                    if (
                        !$precioProductoVariante
                    ) {

                        throw ValidationException::withMessages([

                            'detalles' => [

                                'Una variante no posee una configuración de precios activa.'

                            ]

                        ]);
                    }

                    $cantidad =
                        (float)
                        $detalle->cantidad;

                    if ($cantidad <= 0) {

                        throw ValidationException::withMessages([

                            'detalles' => [

                                'La cantidad de un detalle debe ser mayor que cero.'

                            ]

                        ]);
                    }

                    /*
                     * Una unidad serializada no puede
                     * manejar cantidades fraccionarias.
                     */
                    if (
                        $tipoControl
                        ===
                        ProductoVariante::SERIALIZADO
                        &&
                        abs(
                            $cantidad
                            - round($cantidad)
                        ) > 0.000001
                    ) {

                        throw ValidationException::withMessages([

                            'detalles' => [

                                'Las variantes serializadas deben recibirse en cantidades enteras.'

                            ]

                        ]);
                    }

                    $costoNetoUnitario =
                        round(

                            (float)
                            $detalle->subtotal
                            / $cantidad,

                            2

                        );

                    if (
                        $costoNetoUnitario <= 0
                    ) {

                        throw ValidationException::withMessages([

                            'detalles' => [

                                'El costo neto unitario debe ser mayor que cero.'

                            ]

                        ]);
                    }

                    $idProductoVariante =
                        $detalle
                            ->id_producto_variante;

                    $precios[$idProductoVariante] =
                        $precioProductoVariante;

                    $costosNetos[$idProductoVariante] =
                        $costoNetoUnitario;
                }

                /*
                |--------------------------------------------------------------------------
                | 2. Crear o bloquear los stocks necesarios
                |--------------------------------------------------------------------------
                |
                | SIN_CONTROL no necesita stock.
                |
                | CANTIDAD y SERIALIZADO sí utilizan
                | stock_sucursal.
                |
                */

                foreach (
                    $detalles as $detalle
                ) {

                    $productoVariante =
                        $detalle
                            ->productoVariante;

                    if (
                        $productoVariante
                            ->tipo_control_inventario
                        ===
                        ProductoVariante::SIN_CONTROL
                    ) {

                        continue;
                    }

                    $idProductoVariante =
                        $detalle
                            ->id_producto_variante;

                    $stockSucursal =
                        $this->repository
                            ->findOrCreateStockSucursalForUpdate(

                                $compra->id_sucursal,

                                $idProductoVariante,

                                $data['id_usuario_recepcion']

                            );

                    $stocks[$idProductoVariante] =
                        $stockSucursal;
                }

                /*
                |--------------------------------------------------------------------------
                | 3. Registrar movimientos e items serializados
                |--------------------------------------------------------------------------
                */

                foreach (
                    $detalles as $detalle
                ) {

                    $idProductoVariante =
                        $detalle
                            ->id_producto_variante;

                    $productoVariante =
                        $detalle
                            ->productoVariante;

                    $tipoControl =
                        $productoVariante
                            ->tipo_control_inventario;

                    /*
                     * SIN_CONTROL no altera existencias.
                     */
                    if (
                        $tipoControl
                        !==
                        ProductoVariante::SIN_CONTROL
                    ) {

                        /** @var StockSucursal $stockSucursal */
                        $stockSucursal =
                            $stocks[$idProductoVariante];

                        /*
                         * Movimiento general de inventario.
                         */
                        $movimiento =
                            $this->movimientoService
                                ->store([

                                    'id_stock_sucursal' =>
                                        $stockSucursal
                                            ->id_stock_sucursal,

                                    'tipo_movimiento' =>
                                        MovimientoInventario::ENTRADA,

                                    'cantidad' =>
                                        $detalle->cantidad,

                                    'motivo' =>
                                        'Entrada por compra '
                                        . $compra
                                            ->codigo_compra,

                                    'tipo_referencia' =>
                                        'COMPRA',

                                    'id_referencia' =>
                                        $compra
                                            ->id_compra,

                                    'observaciones' =>
                                        'Entrada automática por recepción de compra.',

                                    'usuario_creacion' =>
                                        $data['id_usuario_recepcion']

                                ]);

                        /*
                         * Si la variante es serializada,
                         * creamos exactamente una unidad
                         * física por cada unidad comprada.
                         */
                        if (
                            $tipoControl
                            ===
                            ProductoVariante::SERIALIZADO
                        ) {

                            $cantidadItems =
                                (int)round(
                                    (float)
                                    $detalle->cantidad
                                );

                            $this
                                ->inventarioItemService
                                ->crearPorEntrada(

                                    $productoVariante,

                                    $stockSucursal,

                                    $movimiento,

                                    $cantidadItems,

                                    $detalle
                                        ->id_compra_detalle,

                                    $data['id_usuario_recepcion']

                                );
                        }
                    }

                    /*
                     * El costo de compra se actualiza
                     * independientemente de si controla
                     * stock o no.
                     */
                    $this->repository
                        ->updateCostoCompra(

                            $precios[$idProductoVariante],

                            $costosNetos[$idProductoVariante],

                            $data['id_usuario_recepcion']

                        );
                }

                /*
                |--------------------------------------------------------------------------
                | 4. Finalizar recepción
                |--------------------------------------------------------------------------
                */

                return $this->repository
                    ->updateState(

                        $compra,

                        [

                            'estado_compra' =>
                                Compra::RECIBIDA,

                            'fecha_recepcion' =>
                                now(),

                            'id_usuario_recepcion' =>
                                $data['id_usuario_recepcion'],

                            'usuario_modificacion' =>
                                $data['id_usuario_recepcion']

                        ]

                    );
            }
        );
    }

    /**
     * Anula una compra que aún no fue recibida.
     */
    public function anular(
        Compra $compra,
        array  $data
    ): Compra
    {

        return DB::transaction(
            function () use (
                $compra,
                $data
            ) {

                $compra =
                    $this->repository
                        ->findByIdForUpdate(

                            $compra->id_compra

                        );

                $this->validarCompraAnulable(
                    $compra
                );

                return $this->repository->updateState(

                    $compra,

                    [
                        'estado_compra' =>
                            Compra::ANULADA,

                        'fecha_anulacion' =>
                            now(),

                        'id_usuario_anulacion' =>
                            $data['id_usuario_anulacion'],

                        'motivo_anulacion' =>
                            $data['motivo_anulacion'],

                        'usuario_modificacion' =>
                            $data['id_usuario_anulacion']
                    ]

                );
            }
        );
    }

    /**
     * Elimina lógicamente una compra en borrador.
     */
    public function destroy(
        Compra $compra
    ): bool
    {

        return DB::transaction(
            function () use ($compra) {

                $compra =
                    $this->repository
                        ->findByIdForUpdate(

                            $compra->id_compra

                        );

                $this->validarCompraBorrador(
                    $compra
                );

                return $this->repository->delete(
                    $compra
                );
            }
        );
    }

    /**
     * Prepara y calcula los detalles.
     */
    private function prepararDetalles(
        array $detallesEntrada,
        float $factorConversion
    ): array
    {

        $detalles = [];

        foreach (
            $detallesEntrada
            as $indice => $detalleEntrada
        ) {

            $cantidad =
                (float)
                $detalleEntrada['cantidad'];

            /*
            |--------------------------------------------------------------------------
            | Cantidad
            |--------------------------------------------------------------------------
            |
            | Las compras de Irriterra se registran actualmente por unidades físicas,
            | por lo que la cantidad debe ser un entero positivo.
            |
            | Esta validación también protege llamadas directas al servicio que no
            | pasan por StoreCompraRequest o UpdateCompraRequest.
            |--------------------------------------------------------------------------
            */

            if (
                $cantidad <= 0
                ||
                floor($cantidad) !== $cantidad
            ) {

                throw ValidationException::withMessages([

                    "detalles.{$indice}.cantidad" => [

                        'La cantidad debe ser un número entero mayor que cero.'

                    ]

                ]);

            }

            /*
             * Después de validar que no contiene parte decimal,
             * trabajamos internamente con un entero.
             */
            $cantidad =
                (int)
                $cantidad;


            $costoUnitarioOriginal =
                (float)
                $detalleEntrada['costo_unitario_original'];


            $descuentoOriginal =
                isset(
                    $detalleEntrada['descuento_original']
                )
                    ? (float)
                $detalleEntrada['descuento_original']
                    : 0;

            /*
            |--------------------------------------------------------------------------
            | Moneda original
            |--------------------------------------------------------------------------
            */

            $importeBrutoOriginal =
                round(

                    $cantidad
                    *
                    $costoUnitarioOriginal,

                    2

                );

            if (
                $descuentoOriginal
                >
                $importeBrutoOriginal
            ) {

                throw ValidationException::withMessages([

                    "detalles.{$indice}.descuento_original" => [

                        'El descuento no puede superar el importe bruto del detalle.'

                    ]

                ]);
            }

            $subtotalOriginal =
                round(

                    $importeBrutoOriginal
                    -
                    $descuentoOriginal,

                    2

                );

            /*
            |--------------------------------------------------------------------------
            | Conversión BOB
            |--------------------------------------------------------------------------
            */

            $costoUnitarioBob =
                round(

                    $costoUnitarioOriginal
                    *
                    $factorConversion,

                    2

                );

            $descuentoBob =
                round(

                    $descuentoOriginal
                    *
                    $factorConversion,

                    2

                );

            /*
             * Calculamos desde el costo unitario BOB
             * ya redondeado para mantener consistencia
             * con lo almacenado.
             */
            $importeBrutoBob =
                round(

                    $cantidad
                    *
                    $costoUnitarioBob,

                    2

                );

            $subtotalBob =
                round(

                    $importeBrutoBob
                    -
                    $descuentoBob,

                    2

                );

            $detalles[] = [

                'id_producto_variante' =>
                    $detalleEntrada['id_producto_variante'],

                'cantidad' =>
                    $cantidad,

                /*
                |--------------------------------------------------------------------------
                | BOB
                |--------------------------------------------------------------------------
                */

                'costo_unitario' =>
                    $costoUnitarioBob,

                'descuento' =>
                    $descuentoBob,

                'subtotal' =>
                    $subtotalBob,

                'importe_bruto' =>
                    $importeBrutoBob,

                /*
                |--------------------------------------------------------------------------
                | Moneda original
                |--------------------------------------------------------------------------
                */

                'costo_unitario_original' =>
                    round(
                        $costoUnitarioOriginal,
                        2
                    ),

                'descuento_original' =>
                    round(
                        $descuentoOriginal,
                        2
                    ),

                'subtotal_original' =>
                    $subtotalOriginal,

                'importe_bruto_original' =>
                    $importeBrutoOriginal,

                'observaciones' =>
                    $detalleEntrada['observaciones']
                    ?? null

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

        /*
        |--------------------------------------------------------------------------
        | BOB
        |--------------------------------------------------------------------------
        */

        $subtotal =
            round(

                collect($detalles)
                    ->sum(

                        fn (array $detalle) =>
                        $detalle[
                        'importe_bruto'
                        ]

                    ),

                2

            );

        $descuento =
            round(

                collect($detalles)
                    ->sum(

                        fn (array $detalle) =>
                        $detalle[
                        'descuento'
                        ]

                    ),

                2

            );

        $total =
            round(

                $subtotal
                -
                $descuento,

                2

            );

        /*
        |--------------------------------------------------------------------------
        | Moneda original
        |--------------------------------------------------------------------------
        */

        $subtotalOriginal =
            round(

                collect($detalles)
                    ->sum(

                        fn (array $detalle) =>
                        $detalle[
                        'importe_bruto_original'
                        ]

                    ),

                2

            );

        $descuentoOriginal =
            round(

                collect($detalles)
                    ->sum(

                        fn (array $detalle) =>
                        $detalle[
                        'descuento_original'
                        ]

                    ),

                2

            );

        $totalOriginal =
            round(

                $subtotalOriginal
                -
                $descuentoOriginal,

                2

            );

        return [

            'subtotal' =>
                $subtotal,

            'descuento' =>
                $descuento,

            'total' =>
                $total,

            'subtotal_original' =>
                $subtotalOriginal,

            'descuento_original' =>
                $descuentoOriginal,

            'total_original' =>
                $totalOriginal

        ];
    }


    private function resolverContextoMoneda(
        array $data,
        ?Compra $compraActual = null
    ): array {

        $moneda =
            strtoupper(
                trim(
                    (string)
                    (
                        $data['moneda']
                        ?? Compra::MONEDA_BOB
                    )
                )
            );

        /*
        |--------------------------------------------------------------------------
        | Compra en BOB
        |--------------------------------------------------------------------------
        */

        if (
            $moneda
            === Compra::MONEDA_BOB
        ) {

            return [

                'factor_conversion' =>
                    1.0,

                'campos_compra' => [

                    'moneda' =>
                        Compra::MONEDA_BOB,

                    'tipo_cambio_referencia' =>
                        null,

                    'tipo_cambio_aplicado' =>
                        1,

                    'origen_tipo_cambio' =>
                        Compra::TC_NO_APLICA,

                    'fuente_tipo_cambio' =>
                        null,

                    'fecha_tipo_cambio' =>
                        null

                ]

            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Compra en USD
        |--------------------------------------------------------------------------
        */

        if (
            $moneda
            !== Compra::MONEDA_USD
        ) {

            throw ValidationException::withMessages([

                'moneda' => [

                    'La moneda de la compra no es válida.'

                ]

            ]);
        }

        $tipoAplicado =
            round(

                (float)
                (
                    $data[
                    'tipo_cambio_aplicado'
                    ]
                    ?? 0
                ),

                6

            );

        if (
            $tipoAplicado <= 0
        ) {

            throw ValidationException::withMessages([

                'tipo_cambio_aplicado' => [

                    'El tipo de cambio aplicado debe ser mayor que cero.'

                ]

            ]);
        }

        $tipoReferencia =
            null;

        if (
            array_key_exists(
                'tipo_cambio_referencia',
                $data
            )
            &&
            $data[
            'tipo_cambio_referencia'
            ] !== null
        ) {

            $tipoReferencia =
                round(

                    (float)
                    $data[
                    'tipo_cambio_referencia'
                    ],

                    6

                );

            if (
                $tipoReferencia <= 0
            ) {

                throw ValidationException::withMessages([

                    'tipo_cambio_referencia' => [

                        'El tipo de cambio de referencia debe ser mayor que cero.'

                    ]

                ]);
            }
        }

        $origen =
            (
                $tipoReferencia !== null
                &&
                abs(
                    $tipoAplicado
                    -
                    $tipoReferencia
                ) < 0.000001
            )

                ? Compra::TC_API

                : Compra::TC_MANUAL;

        /*
         * Si no cambió la configuración monetaria
         * durante una edición, conservamos la fecha
         * original de referencia.
         */
        $mantenerFecha =
            $compraActual !== null
            &&
            $compraActual->moneda
            === Compra::MONEDA_USD
            &&
            abs(
                (float)
                $compraActual
                    ->tipo_cambio_aplicado
                -
                $tipoAplicado
            ) < 0.000001
            &&
            (
                (
                    $compraActual
                        ->tipo_cambio_referencia
                    === null
                    &&
                    $tipoReferencia
                    === null
                )
                ||
                (
                    $compraActual
                        ->tipo_cambio_referencia
                    !== null
                    &&
                    $tipoReferencia
                    !== null
                    &&
                    abs(
                        (float)
                        $compraActual
                            ->tipo_cambio_referencia
                        -
                        $tipoReferencia
                    ) < 0.000001
                )
            );

        return [

            'factor_conversion' =>
                $tipoAplicado,

            'campos_compra' => [

                'moneda' =>
                    Compra::MONEDA_USD,

                'tipo_cambio_referencia' =>
                    $tipoReferencia,

                'tipo_cambio_aplicado' =>
                    $tipoAplicado,

                'origen_tipo_cambio' =>
                    $origen,

                'fuente_tipo_cambio' =>

                    $tipoReferencia !== null

                        ? 'Banco Central de Bolivia'

                        : null,

                'fecha_tipo_cambio' =>

                    $mantenerFecha

                        ? $compraActual
                        ->fecha_tipo_cambio

                        : now()

            ]

        ];
    }

    /**
     * Verifica que la compra esté en borrador.
     */
    private function validarCompraBorrador(
        Compra $compra
    ): void
    {

        if (
            $compra->estado_compra
            !== Compra::BORRADOR
        ) {

            throw ValidationException::withMessages([

                'estado_compra' => [

                    'Solo pueden modificarse o eliminarse compras en borrador.'

                ]

            ]);
        }
    }

    /**
     * Verifica que la compra esté confirmada.
     */
    private function validarCompraConfirmada(
        Compra $compra
    ): void
    {

        if (
            $compra->estado_compra
            !== Compra::CONFIRMADA
        ) {

            throw ValidationException::withMessages([

                'estado_compra' => [

                    'Solo pueden recibirse compras confirmadas.'

                ]

            ]);
        }
    }

    /**
     * Verifica que la compra pueda anularse.
     */
    private function validarCompraAnulable(
        Compra $compra
    ): void
    {

        $estadosPermitidos = [

            Compra::BORRADOR,

            Compra::CONFIRMADA

        ];

        if (
            !in_array(
                $compra->estado_compra,
                $estadosPermitidos,
                true
            )
        ) {

            throw ValidationException::withMessages([

                'estado_compra' => [

                    'Solo pueden anularse compras en borrador o confirmadas.'

                ]

            ]);
        }
    }

    /**
     * Verifica que la compra tenga detalles activos
     * y un total válido.
     */
    private function validarDetallesCompra(
        Compra $compra
    ): void
    {

        if (
            $compra->detalles->isEmpty()
        ) {

            throw ValidationException::withMessages([

                'detalles' => [

                    'La compra debe contener al menos un detalle activo.'

                ]

            ]);
        }

        if (
            $this->convertirACentavos(
                $compra->total
            ) <= 0
        ) {

            throw ValidationException::withMessages([

                'total' => [

                    'El total de la compra debe ser mayor que cero.'

                ]

            ]);
        }
    }

    /**
     * Convierte un importe decimal a centavos.
     */
    private function convertirACentavos(
        float|int|string $importe
    ): int
    {

        return (int)round(

            (float)$importe * 100

        );
    }
}
