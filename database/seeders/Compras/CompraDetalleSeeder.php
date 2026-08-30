<?php

namespace Database\Seeders\Compras;

use App\Models\Compras\Compra;
use App\Models\Compras\CompraDetalle;
use App\Models\Inventario\PrecioProductoVariante;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompraDetalleSeeder extends Seeder
{
    /**
     * Ejecuta el seeder.
     */
    public function run(): void
    {
        $compra =
            Compra::where(
                'estado_registro',
                'A'
            )
                ->where(
                    'estado_compra',
                    Compra::BORRADOR
                )
                ->orderBy(
                    'id_compra'
                )
                ->first();

        $precioProductoVariante =
            PrecioProductoVariante::where(
                'estado_registro',
                'A'
            )
                ->orderBy(
                    'id_precio_producto_variante'
                )
                ->first();

        if (
            ! $compra
            || ! $precioProductoVariante
        ) {

            return;

        }

        DB::transaction(
            function () use (
                $compra,
                $precioProductoVariante
            ): void {

                $cantidad = 5;

                $costoUnitario =
                    (float)
                    $precioProductoVariante
                        ->costo_compra;

                $descuento = 0;

                $importeBruto = round(

                    $cantidad
                    * $costoUnitario,

                    2

                );

                $subtotal = round(

                    $importeBruto
                    - $descuento,

                    2

                );

                CompraDetalle::updateOrCreate(

                    [
                        'id_compra' =>
                            $compra->id_compra,

                        'id_producto_variante' =>
                            $precioProductoVariante
                                ->id_producto_variante
                    ],

                    [
                        'cantidad' =>
                            $cantidad,

                        'costo_unitario' =>
                            $costoUnitario,

                        'descuento' =>
                            $descuento,

                        'subtotal' =>
                            $subtotal,

                        'observaciones' =>
                            'Detalle inicial generado desde el seeder.',

                        'estado_registro' =>
                            'A',

                        'usuario_creacion' =>
                            null,

                        'usuario_modificacion' =>
                            null
                    ]

                );

                $compra->update([

                    'subtotal' =>
                        $importeBruto,

                    'descuento' =>
                        $descuento,

                    'total' =>
                        $subtotal

                ]);

            }
        );
    }
}