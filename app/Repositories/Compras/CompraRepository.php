<?php

namespace App\Repositories\Compras;

use App\Models\Compras\Compra;
use App\Models\Compras\CompraDetalle;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Inventario\PrecioProductoVariante;
use App\Models\Inventario\StockSucursal;

class CompraRepository
{
    /**
     * Relaciones cargadas con la compra.
     */
    private const RELATIONS = [

        'sucursal',

        'proveedor',

        'comprador',

        'usuarioConfirmacion',

        'usuarioRecepcion',

        'usuarioAnulacion',

        'detalles.productoVariante.producto.categoria'

    ];

    /**
     * Obtiene todas las compras activas.
     */
    public function getAll(): Collection
    {
        return Compra::with(
            self::RELATIONS
        )
            ->where(
                'estado_registro',
                'A'
            )
            ->orderByDesc(
                'fecha_compra'
            )
            ->orderByDesc(
                'id_compra'
            )
            ->get();
    }

    /**
     * Busca una compra activa por ID.
     */
    public function findById(
        int $id
    ): Compra {

        return Compra::with(
            self::RELATIONS
        )
            ->where(
                'estado_registro',
                'A'
            )
            ->findOrFail($id);
    }

    /**
     * Busca y bloquea una compra para actualización.
     */
    public function findByIdForUpdate(
        int $id
    ): Compra {

        $compra = Compra::where(
            'estado_registro',
            'A'
        )
            ->lockForUpdate()
            ->findOrFail($id);

        return $compra->load(
            self::RELATIONS
        );
    }

    /**
     * Registra una compra y sus detalles.
     */
    public function create(
        array $data,
        array $detalles
    ): Compra {

        $compra = Compra::create(
            $data
        );

        $this->syncDetalles(

            $compra,

            $detalles,

            $data['usuario_creacion'] ?? null,

            null

        );

        return $compra->load(
            self::RELATIONS
        );
    }

    /**
     * Actualiza una compra en borrador.
     */
    public function update(
        Compra $compra,
        array $data,
        ?array $detalles = null
    ): Compra {

        $compra->update(
            $data
        );

        if ($detalles !== null) {

            $this->syncDetalles(

                $compra,

                $detalles,

                null,

                $data['usuario_modificacion'] ?? null

            );
        }

        return $compra->fresh(
            self::RELATIONS
        );
    }

    /**
     * Actualiza el estado y datos operativos.
     */
    public function updateState(
        Compra $compra,
        array $data
    ): Compra {

        $compra->update(
            $data
        );

        return $compra->fresh(
            self::RELATIONS
        );
    }

    /**
     * Realiza la eliminación lógica.
     */
    public function delete(
        Compra $compra
    ): bool {

        CompraDetalle::where(
            'id_compra',
            $compra->id_compra
        )
            ->where(
                'estado_registro',
                'A'
            )
            ->update([

                'estado_registro' =>
                'I',

                'usuario_modificacion' =>
                $compra->usuario_modificacion

            ]);

        return $compra->update([

            'estado_registro' =>
            'I'

        ]);
    }

    /**
     * Obtiene y bloquea la configuración de precios
     * de una variante.
     */
    public function findPrecioProductoVarianteForUpdate(
        int $idProductoVariante
    ): ?PrecioProductoVariante {

        return PrecioProductoVariante::where(
            'id_producto_variante',
            $idProductoVariante
        )
            ->where(
                'estado_registro',
                'A'
            )
            ->lockForUpdate()
            ->first();
    }

    /**
     * Obtiene, crea o reactiva el stock de una
     * variante dentro de una sucursal.
     */
    public function findOrCreateStockSucursalForUpdate(
        int $idSucursal,
        int $idProductoVariante,
        ?int $idUsuario
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
            ->lockForUpdate()
            ->first();

        if (! $stockSucursal) {

            $stockSucursal =
                StockSucursal::create([

                    'id_sucursal' =>
                    $idSucursal,

                    'id_producto_variante' =>
                    $idProductoVariante,

                    'stock_actual' =>
                    0,

                    'stock_minimo' =>
                    0,

                    'stock_maximo' =>
                    null,

                    'ubicacion_almacen' =>
                    null,

                    'observaciones' =>
                    'Stock creado automáticamente mediante recepción de compra.',

                    'estado_registro' =>
                    'A',

                    'usuario_creacion' =>
                    $idUsuario,

                    'usuario_modificacion' =>
                    null

                ]);
        } elseif (
            $stockSucursal->estado_registro
            !== 'A'
        ) {

            $stockSucursal->update([

                'estado_registro' =>
                'A',

                'usuario_modificacion' =>
                $idUsuario

            ]);
        }

        return StockSucursal::where(
            'id_stock_sucursal',
            $stockSucursal->id_stock_sucursal
        )
            ->lockForUpdate()
            ->firstOrFail();
    }

    /**
     * Actualiza el último costo neto de compra
     * configurado para una variante.
     */
    public function updateCostoCompra(
        PrecioProductoVariante $precioProductoVariante,
        float $costoCompra,
        ?int $idUsuario
    ): PrecioProductoVariante {

        $precioProductoVariante->update([

            'costo_compra' =>
            round(
                $costoCompra,
                2
            ),

            'usuario_modificacion' =>
            $idUsuario

        ]);

        return $precioProductoVariante->fresh();
    }

    /**
     * Sincroniza los detalles de una compra.
     */
    private function syncDetalles(
        Compra $compra,
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
                fn($id) => (int) $id
            )
            ->all();

        /*
         * Los detalles retirados de la petición
         * se desactivan lógicamente.
         */
        CompraDetalle::where(
            'id_compra',
            $compra->id_compra
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

                'estado_registro' =>
                'I',

                'usuario_modificacion' =>
                $usuarioModificacion

            ]);

        foreach ($detalles as $detalle) {

            /*
             * Permite recuperar un detalle previamente
             * desactivado sin violar la restricción única.
             */
            $compraDetalle =
                CompraDetalle::firstOrNew([

                    'id_compra' =>
                    $compra->id_compra,

                    'id_producto_variante' =>
                    $detalle['id_producto_variante']

                ]);

            $compraDetalle->cantidad =
                $detalle['cantidad'];

            $compraDetalle->costo_unitario =
                $detalle['costo_unitario'];

            $compraDetalle->descuento =
                $detalle['descuento'];

            $compraDetalle->subtotal =
                $detalle['subtotal'];

            $compraDetalle->observaciones =
                $detalle['observaciones'] ?? null;

            $compraDetalle->estado_registro =
                'A';

            if (! $compraDetalle->exists) {

                $compraDetalle->usuario_creacion =
                    $usuarioCreacion;
            } else {

                $compraDetalle->usuario_modificacion =
                    $usuarioModificacion;
            }

            $compraDetalle->save();
        }
    }
}
