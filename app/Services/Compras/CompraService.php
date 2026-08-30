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

class CompraService
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly CompraRepository $repository,
        private readonly MovimientoInventarioService $movimientoService
    ) {}

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
    ): Compra {

        return $this->repository->findById(
            $id
        );
    }

    /**
     * Registra una compra en borrador.
     */
    public function store(
        array $data
    ): Compra {

        return DB::transaction(
            function () use ($data) {

                $detallesEntrada =
                    $data['detalles'];

                unset(
                    $data['detalles']
                );

                $detalles =
                    $this->prepararDetalles(
                        $detallesEntrada
                    );

                $totales =
                    $this->calcularTotales(
                        $detalles
                    );

                $data['codigo_compra'] =
                    'COM-' . Str::upper(
                        (string) Str::ulid()
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

                $data['subtotal'] =
                    $totales['subtotal'];

                $data['descuento'] =
                    $totales['descuento'];

                $data['total'] =
                    $totales['total'];

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

                return $this->repository->create(

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
        array $data
    ): Compra {

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

                $detalles = null;

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

                    $data['codigo_compra'],

                    $data['estado_compra'],

                    $data['fecha_confirmacion'],

                    $data['fecha_recepcion'],

                    $data['fecha_anulacion'],

                    $data['subtotal'],

                    $data['descuento'],

                    $data['total'],

                    $data['id_usuario_confirmacion'],

                    $data['id_usuario_recepcion'],

                    $data['id_usuario_anulacion'],

                    $data['motivo_anulacion'],

                    $data['estado_registro'],

                    $data['usuario_creacion']

                );

                /*
                 * Los totales calculados deben conservarse.
                 */
                if ($detalles !== null) {

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

                return $this->repository->update(

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
        array $data
    ): Compra {

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
        array $data
    ): Compra {

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
             * Orden consistente para disminuir
             * el riesgo de bloqueos cruzados.
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
             * Primero se validan y bloquean todas
             * las configuraciones de precios.
             */
                foreach (
                    $detalles as $detalle
                ) {

                    $precioProductoVariante =
                        $this->repository
                        ->findPrecioProductoVarianteForUpdate(

                            $detalle
                                ->id_producto_variante

                        );

                    if (
                        ! $precioProductoVariante
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

                    $precios[$idProductoVariante] = $precioProductoVariante;

                    $costosNetos[$idProductoVariante] = $costoNetoUnitario;
                }

                /*
             * Después se crean, reactivan o bloquean
             * todos los registros de stock.
             */
                foreach (
                    $detalles as $detalle
                ) {

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

                    $stocks[$idProductoVariante] = $stockSucursal;
                }

                /*
             * Una vez validados todos los detalles,
             * se generan los movimientos y se
             * actualizan los costos.
             */
                foreach (
                    $detalles as $detalle
                ) {

                    $idProductoVariante =
                        $detalle
                        ->id_producto_variante;

                    /** @var StockSucursal $stockSucursal */
                    $stockSucursal =
                        $stocks[$idProductoVariante];

                    $this->movimientoService->store([

                        'id_stock_sucursal' =>
                        $stockSucursal
                            ->id_stock_sucursal,

                        'tipo_movimiento' =>
                        MovimientoInventario::ENTRADA,

                        'cantidad' =>
                        $detalle->cantidad,

                        'motivo' =>
                        'Entrada por compra '
                            . $compra->codigo_compra,

                        'tipo_referencia' =>
                        'COMPRA',

                        'id_referencia' =>
                        $compra->id_compra,

                        'observaciones' =>
                        'Entrada automática por recepción de compra.',

                        'usuario_creacion' =>
                        $data['id_usuario_recepcion']

                    ]);

                    $this->repository->updateCostoCompra(

                        $precios[$idProductoVariante],

                        $costosNetos[$idProductoVariante],

                        $data['id_usuario_recepcion']

                    );
                }

                return $this->repository->updateState(

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
        array $data
    ): Compra {

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
    ): bool {

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
        array $detallesEntrada
    ): array {

        $detalles = [];

        foreach (
            $detallesEntrada
            as $indice => $detalleEntrada
        ) {

            $cantidad =
                (float)
                $detalleEntrada['cantidad'];

            $costoUnitario =
                (float)
                $detalleEntrada['costo_unitario'];

            $descuento =
                isset(
                    $detalleEntrada['descuento']
                )
                ? (float)
                $detalleEntrada['descuento']
                : 0;

            $importeBruto = round(

                $cantidad
                    * $costoUnitario,

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

                $importeBruto
                    - $descuento,

                2

            );

            $detalles[] = [

                'id_producto_variante' =>
                $detalleEntrada['id_producto_variante'],

                'cantidad' =>
                $cantidad,

                'costo_unitario' =>
                round(
                    $costoUnitario,
                    2
                ),

                'descuento' =>
                round(
                    $descuento,
                    2
                ),

                'subtotal' =>
                $subtotalDetalle,

                /*
                 * Campo interno utilizado solamente
                 * para calcular el subtotal general.
                 */
                'importe_bruto' =>
                $importeBruto,

                'observaciones' =>
                $detalleEntrada['observaciones'] ?? null

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

                fn(array $detalle) =>
                $detalle['importe_bruto']

            ),

            2

        );

        $descuento = round(

            collect($detalles)->sum(

                fn(array $detalle) =>
                $detalle['descuento']

            ),

            2

        );

        $total = round(

            $subtotal
                - $descuento,

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
     * Verifica que la compra esté en borrador.
     */
    private function validarCompraBorrador(
        Compra $compra
    ): void {

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
    ): void {

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
    ): void {

        $estadosPermitidos = [

            Compra::BORRADOR,

            Compra::CONFIRMADA

        ];

        if (
            ! in_array(
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
    ): void {

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
    ): int {

        return (int) round(

            (float) $importe * 100

        );
    }
}
