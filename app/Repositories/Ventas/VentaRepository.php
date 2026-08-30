<?php

namespace App\Repositories\Ventas;

use App\Models\Ventas\Venta;
use App\Models\Ventas\VentaDetalle;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Inventario\StockSucursal;

class VentaRepository
{
    /**
     * Relaciones cargadas con la venta.
     */
    private const RELATIONS = [

        'sucursal',

        'cliente',

        'vendedor',

        'usuarioAnulacion',

        'detalles.productoVariante.producto.categoria'

    ];

    /**
     * Obtiene todas las ventas activas.
     */
    public function getAll(): Collection
    {
        return Venta::with(
            self::RELATIONS
        )
            ->where(
                'estado_registro',
                'A'
            )
            ->orderByDesc(
                'fecha_venta'
            )
            ->orderByDesc(
                'id_venta'
            )
            ->get();
    }

    /**
     * Busca una venta activa por ID.
     */
    public function findById(
        int $id
    ): Venta {

        return Venta::with(
            self::RELATIONS
        )
            ->where(
                'estado_registro',
                'A'
            )
            ->findOrFail($id);
    }

    /**
     * Busca y bloquea una venta.
     */
    public function findByIdForUpdate(
        int $id
    ): Venta {

        $venta = Venta::where(
            'estado_registro',
            'A'
        )
            ->lockForUpdate()
            ->findOrFail($id);

        return $venta->load(
            self::RELATIONS
        );
    }

    /**
     * Registra una venta y sus detalles.
     */
    public function create(
        array $data,
        array $detalles
    ): Venta {

        $venta = Venta::create($data);

        $this->syncDetalles(

            $venta,

            $detalles,

            $data['usuario_creacion'] ?? null,

            null

        );

        return $venta->load(
            self::RELATIONS
        );
    }

    /**
     * Actualiza una venta en borrador.
     */
    public function update(
        Venta $venta,
        array $data,
        ?array $detalles = null
    ): Venta {

        $venta->update($data);

        if ($detalles !== null) {

            $this->syncDetalles(

                $venta,

                $detalles,

                null,

                $data['usuario_modificacion'] ?? null

            );
        }

        return $venta->fresh(
            self::RELATIONS
        );
    }

    /**
     * Realiza la eliminación lógica.
     */
    public function delete(
        Venta $venta
    ): bool {

        VentaDetalle::where(
            'id_venta',
            $venta->id_venta
        )
            ->where(
                'estado_registro',
                'A'
            )
            ->update([

                'estado_registro' =>
                'I',

                'usuario_modificacion' =>
                $venta->usuario_modificacion

            ]);

        return $venta->update([

            'estado_registro' =>
            'I'

        ]);
    }

    /**
     * Actualiza el estado y los datos operativos.
     */
    public function updateState(
        Venta $venta,
        array $data
    ): Venta {

        $venta->update($data);

        return $venta->fresh(
            self::RELATIONS
        );
    }

    /**
     * Busca y bloquea el stock de una variante
     * dentro de una sucursal.
     */
    public function findStockBySucursalVarianteForUpdate(
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
            ->lockForUpdate()
            ->first();
    }

    /**
     * Sincroniza los detalles de una venta.
     */
    private function syncDetalles(
        Venta $venta,
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
        VentaDetalle::where(
            'id_venta',
            $venta->id_venta
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
             * firstOrNew permite recuperar un detalle
             * anteriormente desactivado sin violar
             * la restricción unique.
             */
            $ventaDetalle =
                VentaDetalle::firstOrNew([

                    'id_venta' =>
                    $venta->id_venta,

                    'id_producto_variante' =>
                    $detalle['id_producto_variante']

                ]);

            $ventaDetalle->cantidad =
                $detalle['cantidad'];

            $ventaDetalle->precio_unitario =
                $detalle['precio_unitario'];

            $ventaDetalle->costo_unitario =
                $detalle['costo_unitario'];

            $ventaDetalle->descuento =
                $detalle['descuento'];

            $ventaDetalle->subtotal =
                $detalle['subtotal'];

            $ventaDetalle->observaciones =
                $detalle['observaciones'] ?? null;

            $ventaDetalle->estado_registro =
                'A';

            if (! $ventaDetalle->exists) {

                $ventaDetalle->usuario_creacion =
                    $usuarioCreacion;
            } else {

                $ventaDetalle->usuario_modificacion =
                    $usuarioModificacion;
            }

            $ventaDetalle->save();
        }
    }
}
