<?php

namespace App\Repositories\Transferencias;

use App\Models\Inventario\StockSucursal;
use App\Models\Transferencias\TransferenciaInventario;
use App\Models\Transferencias\TransferenciaInventarioDetalle;
use Illuminate\Database\Eloquent\Collection;

class TransferenciaInventarioRepository
{
    /**
     * Relaciones del recurso.
     */
    private const RELATIONS = [

        'sucursalOrigen',

        'sucursalDestino',

        'detalles.productoVariante.producto.categoria'

    ];

    /**
     * Lista transferencias activas.
     */
    public function getAll(): Collection
    {
        return TransferenciaInventario::with(
            self::RELATIONS
        )
            ->where(
                'estado_registro',
                'A'
            )
            ->orderByDesc(
                'fecha_solicitud'
            )
            ->orderByDesc(
                'id_transferencia_inventario'
            )
            ->get();
    }

    /**
     * Busca una transferencia por ID.
     */
    public function findById(
        int $id
    ): TransferenciaInventario {

        return TransferenciaInventario::with(
            self::RELATIONS
        )
            ->findOrFail($id);
    }

    /**
     * Obtiene y bloquea una transferencia.
     */
    public function findByIdForUpdate(
        int $id
    ): TransferenciaInventario {

        $transferenciaInventario =
            TransferenciaInventario::where(
                'estado_registro',
                'A'
            )
                ->lockForUpdate()
                ->findOrFail($id);

        return $transferenciaInventario->load(
            self::RELATIONS
        );
    }

    /**
     * Registra una transferencia y sus detalles.
     */
    public function create(
        array $data,
        array $detalles
    ): TransferenciaInventario {

        $transferenciaInventario =
            TransferenciaInventario::create($data);

        $this->syncDetalles(

            $transferenciaInventario,

            $detalles,

            $data['usuario_creacion'] ?? null,

            null

        );

        return $transferenciaInventario->load(
            self::RELATIONS
        );
    }

    /**
     * Actualiza una transferencia pendiente.
     */
    public function update(
        TransferenciaInventario $transferenciaInventario,
        array $data,
        ?array $detalles = null
    ): TransferenciaInventario {

        $transferenciaInventario->update($data);

        if ($detalles !== null) {

            $this->syncDetalles(

                $transferenciaInventario,

                $detalles,

                null,

                $data['usuario_modificacion'] ?? null

            );

        }

        return $transferenciaInventario->fresh(
            self::RELATIONS
        );
    }

    /**
     * Actualiza el estado de una transferencia.
     */
    public function updateState(
        TransferenciaInventario $transferenciaInventario,
        array $data
    ): TransferenciaInventario {

        $transferenciaInventario->update($data);

        return $transferenciaInventario->fresh(
            self::RELATIONS
        );
    }

    /**
     * Eliminación lógica.
     */
    public function delete(
        TransferenciaInventario $transferenciaInventario
    ): bool {

        TransferenciaInventarioDetalle::where(
            'id_transferencia_inventario',
            $transferenciaInventario
                ->id_transferencia_inventario
        )
            ->where(
                'estado_registro',
                'A'
            )
            ->update([

                'estado_registro' => 'I',

                'usuario_modificacion' =>
                    $transferenciaInventario
                        ->usuario_modificacion

            ]);

        return $transferenciaInventario->update([

            'estado_registro' => 'I'

        ]);
    }

    /**
     * Busca el stock de una variante en una sucursal.
     */
    public function findStockBySucursalVariante(
        int $idSucursal,
        int $idProductoVariante
    ): ?StockSucursal {

        return StockSucursal::where(
            'id_sucursal',
            $idSucursal
        )
            ->where(
                'id_producto_variante',
                $idProductoVariante
            )
            ->where(
                'estado_registro',
                'A'
            )
            ->first();
    }

    /**
     * Obtiene o crea el stock de destino.
     */
    public function findOrCreateStockDestino(
        int $idSucursal,
        int $idProductoVariante
    ): StockSucursal {

        $stockSucursal =
            StockSucursal::where(
                'id_sucursal',
                $idSucursal
            )
                ->where(
                    'id_producto_variante',
                    $idProductoVariante
                )
                ->first();

        if (! $stockSucursal) {

            return StockSucursal::create([

                'id_sucursal' =>
                    $idSucursal,

                'id_producto_variante' =>
                    $idProductoVariante,

                'stock_actual' => 0,

                'stock_minimo' => 0,

                'stock_maximo' => null,

                'ubicacion_almacen' => null,

                'observaciones' =>
                    'Registro creado automáticamente por transferencia.',

                'estado_registro' => 'A',

                'usuario_creacion' => null,

                'usuario_modificacion' => null

            ]);

        }

        if (
            $stockSucursal->estado_registro === 'I'
        ) {

            $stockSucursal->update([

                'estado_registro' => 'A'

            ]);

        }

        return $stockSucursal->fresh();
    }

    /**
     * Sincroniza los detalles de la transferencia.
     */
    private function syncDetalles(
        TransferenciaInventario $transferenciaInventario,
        array $detalles,
        ?int $usuarioCreacion,
        ?int $usuarioModificacion
    ): void {

        $idsProductoVariante = collect(
            $detalles
        )
            ->pluck(
                'id_producto_variante'
            )
            ->map(
                fn ($id) => (int) $id
            )
            ->all();

        TransferenciaInventarioDetalle::where(
            'id_transferencia_inventario',
            $transferenciaInventario
                ->id_transferencia_inventario
        )
            ->where(
                'estado_registro',
                'A'
            )
            ->whereNotIn(
                'id_producto_variante',
                $idsProductoVariante
            )
            ->update([

                'estado_registro' => 'I',

                'usuario_modificacion' =>
                    $usuarioModificacion

            ]);

        foreach ($detalles as $detalle) {

            $registro =
                TransferenciaInventarioDetalle::firstOrNew([

                    'id_transferencia_inventario' =>
                        $transferenciaInventario
                            ->id_transferencia_inventario,

                    'id_producto_variante' =>
                        $detalle[
                            'id_producto_variante'
                        ]

                ]);

            $registro->cantidad =
                $detalle['cantidad'];

            $registro->observaciones =
                $detalle['observaciones'] ?? null;

            $registro->estado_registro = 'A';

            if (! $registro->exists) {

                $registro->usuario_creacion =
                    $usuarioCreacion;

            } else {

                $registro->usuario_modificacion =
                    $usuarioModificacion;

            }

            $registro->save();

        }
    }
}