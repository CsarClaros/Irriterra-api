<?php

namespace App\Services\Transferencias;

use App\Models\Inventario\MovimientoInventario;
use App\Models\Transferencias\TransferenciaInventario;
use App\Repositories\Transferencias\TransferenciaInventarioRepository;
use App\Services\Inventario\MovimientoInventarioService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TransferenciaInventarioService
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly TransferenciaInventarioRepository $repository,
        private readonly MovimientoInventarioService $movimientoService
    ) {
    }

    /**
     * Lista transferencias activas.
     */
    public function index()
    {
        return $this->repository->getAll();
    }

    /**
     * Muestra una transferencia.
     */
    public function show(
        int $id
    ): TransferenciaInventario {

        return $this->repository->findById(
            $id
        );
    }

    /**
     * Registra una transferencia pendiente.
     */
    public function store(
        array $data
    ): TransferenciaInventario {

        return DB::transaction(
            function () use ($data) {

                $detalles = $data['detalles'];

                unset($data['detalles']);

                $data['codigo_transferencia'] =
                    'TRF-' . Str::upper(
                        (string) Str::ulid()
                    );

                $data['estado_transferencia'] =
                    TransferenciaInventario::PENDIENTE;

                $data['fecha_solicitud'] =
                    $data['fecha_solicitud']
                        ?? now();

                $data['estado_registro'] = 'A';

                return $this->repository->create(

                    $data,

                    $detalles

                );

            }
        );
    }

    /**
     * Actualiza una transferencia pendiente.
     */
    public function update(
        TransferenciaInventario $transferenciaInventario,
        array $data
    ): TransferenciaInventario {

        return DB::transaction(
            function () use (
                $transferenciaInventario,
                $data
            ) {

                $transferenciaInventario =
                    $this->repository
                        ->findByIdForUpdate(

                            $transferenciaInventario
                                ->id_transferencia_inventario

                        );

                $this->validarEstado(

                    $transferenciaInventario,

                    [
                        TransferenciaInventario::PENDIENTE
                    ],

                    'Solo pueden actualizarse transferencias pendientes.'

                );

                $detalles =
                    $data['detalles'] ?? null;

                unset($data['detalles']);

                return $this->repository->update(

                    $transferenciaInventario,

                    $data,

                    $detalles

                );

            }
        );
    }

    /**
     * Envía la transferencia.
     */
    public function enviar(
        TransferenciaInventario $transferenciaInventario,
        array $data
    ): TransferenciaInventario {

        return DB::transaction(
            function () use (
                $transferenciaInventario,
                $data
            ) {

                $transferenciaInventario =
                    $this->repository
                        ->findByIdForUpdate(

                            $transferenciaInventario
                                ->id_transferencia_inventario

                        );

                $this->validarEstado(

                    $transferenciaInventario,

                    [
                        TransferenciaInventario::PENDIENTE
                    ],

                    'Solo puede enviarse una transferencia pendiente.'

                );

                $this->validarDetalles(
                    $transferenciaInventario
                );

                foreach (
                    $transferenciaInventario->detalles
                    as $detalle
                ) {

                    $stockOrigen =
                        $this->repository
                            ->findStockBySucursalVariante(

                                $transferenciaInventario
                                    ->id_sucursal_origen,

                                $detalle
                                    ->id_producto_variante

                            );

                    if (! $stockOrigen) {

                        throw ValidationException::withMessages([

                            'detalles' => [

                                'Una variante no posee stock activo en la sucursal de origen.'

                            ]

                        ]);

                    }

                    $this->movimientoService->store([

                        'id_stock_sucursal' =>
                            $stockOrigen
                                ->id_stock_sucursal,

                        'tipo_movimiento' =>
                            MovimientoInventario::SALIDA,

                        'cantidad' =>
                            $detalle->cantidad,

                        'motivo' =>
                            'Salida por transferencia '
                            . $transferenciaInventario
                                ->codigo_transferencia,

                        'tipo_referencia' =>
                            'TRANSFERENCIA',

                        'id_referencia' =>
                            $transferenciaInventario
                                ->id_transferencia_inventario,

                        'observaciones' =>
                            'Salida de la sucursal de origen.',

                        'usuario_creacion' =>
                            $data['id_usuario_envio']

                    ]);

                }

                return $this->repository->updateState(

                    $transferenciaInventario,

                    [
                        'estado_transferencia' =>
                            TransferenciaInventario::EN_TRANSITO,

                        'fecha_envio' => now(),

                        'id_usuario_envio' =>
                            $data['id_usuario_envio'],

                        'usuario_modificacion' =>
                            $data['id_usuario_envio']
                    ]

                );

            }
        );
    }

    /**
     * Completa una transferencia.
     */
    public function completar(
        TransferenciaInventario $transferenciaInventario,
        array $data
    ): TransferenciaInventario {

        return DB::transaction(
            function () use (
                $transferenciaInventario,
                $data
            ) {

                $transferenciaInventario =
                    $this->repository
                        ->findByIdForUpdate(

                            $transferenciaInventario
                                ->id_transferencia_inventario

                        );

                $this->validarEstado(

                    $transferenciaInventario,

                    [
                        TransferenciaInventario::EN_TRANSITO
                    ],

                    'Solo puede completarse una transferencia en tránsito.'

                );

                $this->validarDetalles(
                    $transferenciaInventario
                );

                foreach (
                    $transferenciaInventario->detalles
                    as $detalle
                ) {

                    $stockDestino =
                        $this->repository
                            ->findOrCreateStockDestino(

                                $transferenciaInventario
                                    ->id_sucursal_destino,

                                $detalle
                                    ->id_producto_variante

                            );

                    $this->movimientoService->store([

                        'id_stock_sucursal' =>
                            $stockDestino
                                ->id_stock_sucursal,

                        'tipo_movimiento' =>
                            MovimientoInventario::ENTRADA,

                        'cantidad' =>
                            $detalle->cantidad,

                        'motivo' =>
                            'Entrada por transferencia '
                            . $transferenciaInventario
                                ->codigo_transferencia,

                        'tipo_referencia' =>
                            'TRANSFERENCIA',

                        'id_referencia' =>
                            $transferenciaInventario
                                ->id_transferencia_inventario,

                        'observaciones' =>
                            'Recepción en la sucursal destino.',

                        'usuario_creacion' =>
                            $data['id_usuario_recepcion']

                    ]);

                }

                return $this->repository->updateState(

                    $transferenciaInventario,

                    [
                        'estado_transferencia' =>
                            TransferenciaInventario::COMPLETADA,

                        'fecha_recepcion' => now(),

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
     * Rechaza una transferencia.
     */
    public function rechazar(
        TransferenciaInventario $transferenciaInventario,
        array $data
    ): TransferenciaInventario {

        return DB::transaction(
            function () use (
                $transferenciaInventario,
                $data
            ) {

                $transferenciaInventario =
                    $this->repository
                        ->findByIdForUpdate(

                            $transferenciaInventario
                                ->id_transferencia_inventario

                        );

                $this->validarEstado(

                    $transferenciaInventario,

                    [
                        TransferenciaInventario::PENDIENTE,

                        TransferenciaInventario::EN_TRANSITO
                    ],

                    'La transferencia ya no puede rechazarse.'

                );

                /*
                 * Si estaba en tránsito, el stock ya fue
                 * descontado del origen y debe restaurarse.
                 */
                if (
                    $transferenciaInventario
                        ->estado_transferencia
                    === TransferenciaInventario::EN_TRANSITO
                ) {

                    foreach (
                        $transferenciaInventario->detalles
                        as $detalle
                    ) {

                        $stockOrigen =
                            $this->repository
                                ->findStockBySucursalVariante(

                                    $transferenciaInventario
                                        ->id_sucursal_origen,

                                    $detalle
                                        ->id_producto_variante

                                );

                        if (! $stockOrigen) {

                            throw ValidationException::withMessages([

                                'detalles' => [

                                    'No se encontró el stock de origen para revertir la transferencia.'

                                ]

                            ]);

                        }

                        $this->movimientoService->store([

                            'id_stock_sucursal' =>
                                $stockOrigen
                                    ->id_stock_sucursal,

                            'tipo_movimiento' =>
                                MovimientoInventario::ENTRADA,

                            'cantidad' =>
                                $detalle->cantidad,

                            'motivo' =>
                                'Reversión de transferencia rechazada '
                                . $transferenciaInventario
                                    ->codigo_transferencia,

                            'tipo_referencia' =>
                                'TRANSFERENCIA',

                            'id_referencia' =>
                                $transferenciaInventario
                                    ->id_transferencia_inventario,

                            'observaciones' =>
                                'Restitución del stock a la sucursal de origen.',

                            'usuario_creacion' =>
                                $data['id_usuario_rechazo']

                        ]);

                    }

                }

                return $this->repository->updateState(

                    $transferenciaInventario,

                    [
                        'estado_transferencia' =>
                            TransferenciaInventario::RECHAZADA,

                        'fecha_rechazo' => now(),

                        'id_usuario_rechazo' =>
                            $data['id_usuario_rechazo'],

                        'motivo_rechazo' =>
                            $data['motivo_rechazo'],

                        'usuario_modificacion' =>
                            $data['id_usuario_rechazo']
                    ]

                );

            }
        );
    }

    /**
     * Eliminación lógica.
     */
    public function destroy(
        TransferenciaInventario $transferenciaInventario
    ): bool {

        return DB::transaction(
            function () use (
                $transferenciaInventario
            ) {

                $transferenciaInventario =
                    $this->repository
                        ->findByIdForUpdate(

                            $transferenciaInventario
                                ->id_transferencia_inventario

                        );

                $this->validarEstado(

                    $transferenciaInventario,

                    [
                        TransferenciaInventario::PENDIENTE,

                        TransferenciaInventario::RECHAZADA
                    ],

                    'No puede eliminarse una transferencia enviada o completada.'

                );

                return $this->repository->delete(
                    $transferenciaInventario
                );

            }
        );
    }

    /**
     * Valida el estado actual.
     */
    private function validarEstado(
        TransferenciaInventario $transferenciaInventario,
        array $estadosPermitidos,
        string $mensaje
    ): void {

        if (
            ! in_array(
                $transferenciaInventario
                    ->estado_transferencia,
                $estadosPermitidos,
                true
            )
        ) {

            throw ValidationException::withMessages([

                'estado_transferencia' => [

                    $mensaje

                ]

            ]);

        }
    }

    /**
     * Verifica que existan detalles activos.
     */
    private function validarDetalles(
        TransferenciaInventario $transferenciaInventario
    ): void {

        if (
            $transferenciaInventario
                ->detalles
                ->isEmpty()
        ) {

            throw ValidationException::withMessages([

                'detalles' => [

                    'La transferencia debe contener al menos un detalle activo.'

                ]

            ]);

        }
    }
}