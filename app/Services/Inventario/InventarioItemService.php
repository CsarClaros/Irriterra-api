<?php

namespace App\Services\Inventario;

use App\Models\Inventario\InventarioItem;
use App\Models\Inventario\MovimientoInventario;
use App\Models\Inventario\ProductoVariante;
use App\Models\Inventario\StockSucursal;
use App\Repositories\Inventario\InventarioItemRepository;
use App\Repositories\Inventario\MovimientoInventarioItemRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InventarioItemService
{
    public function __construct(
        private readonly InventarioItemRepository           $repository,
        private readonly MovimientoInventarioItemRepository $movimientoItemRepository
    )
    {
    }

    public function index(): Collection
    {
        return $this->repository
            ->getAll();
    }

    public function show(
        int $id
    ): InventarioItem
    {

        return $this->repository
            ->findById($id);
    }

    /**
     * Crea las unidades físicas correspondientes
     * a una entrada de inventario serializada.
     */
    public function crearPorEntrada(
        ProductoVariante     $productoVariante,
        StockSucursal        $stockSucursal,
        MovimientoInventario $movimiento,
        int                  $cantidad,
        ?int                 $idCompraDetalleOrigen,
        ?int                 $usuario
    ): array
    {

        if (
            $productoVariante
                ->tipo_control_inventario
            !== ProductoVariante::SERIALIZADO
        ) {

            throw ValidationException::withMessages([

                'tipo_control_inventario' => [

                    'Solo pueden generarse items para variantes serializadas.'

                ]

            ]);
        }

        if ($cantidad <= 0) {

            throw ValidationException::withMessages([

                'cantidad' => [

                    'La cantidad de items debe ser mayor que cero.'

                ]

            ]);
        }

        return DB::transaction(
            function () use (
                $productoVariante,
                $stockSucursal,
                $movimiento,
                $cantidad,
                $idCompraDetalleOrigen,
                $usuario
            ) {

                $items = [];

                for (
                    $i = 0;
                    $i < $cantidad;
                    $i++
                ) {

                    $item =
                        $this->repository
                            ->create([

                                'id_producto_variante' =>
                                    $productoVariante
                                        ->id_producto_variante,

                                'id_stock_sucursal' =>
                                    $stockSucursal
                                        ->id_stock_sucursal,

                                'id_compra_detalle_origen' =>
                                    $idCompraDetalleOrigen,

                                'numero_serie' =>
                                    $this
                                        ->generarNumeroSerie(),

                                'estado_item' =>
                                    InventarioItem::DISPONIBLE,

                                'fecha_ingreso' =>
                                    now(),

                                'estado_registro' =>
                                    'A',

                                'usuario_creacion' =>
                                    $usuario,

                                'usuario_modificacion' =>
                                    $usuario

                            ]);

                    $this
                        ->movimientoItemRepository
                        ->create([

                            'id_movimiento_inventario' =>
                                $movimiento
                                    ->id_movimiento_inventario,

                            'id_inventario_item' =>
                                $item
                                    ->id_inventario_item,

                            'estado_anterior' =>
                                null,

                            'estado_resultante' =>
                                InventarioItem::DISPONIBLE,

                            'id_stock_sucursal_anterior' =>
                                null,

                            'id_stock_sucursal_resultante' =>
                                $stockSucursal
                                    ->id_stock_sucursal,

                            'observaciones' =>
                                'Ingreso inicial de unidad serializada.',

                            'estado_registro' =>
                                'A',

                            'usuario_creacion' =>
                                $usuario

                        ]);

                    $items[] = $item;
                }

                return $items;
            }
        );
    }

    private function generarNumeroSerie(): string
    {
        return 'SER-'
            . Str::upper(
                (string)Str::ulid()
            );
    }
}
