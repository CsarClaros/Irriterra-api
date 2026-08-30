<?php

namespace Database\Seeders\Ventas;

use App\Models\Inventario\PrecioProductoVariante;
use App\Models\Ventas\Venta;
use App\Models\Ventas\VentaDetalle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VentaDetalleSeeder extends Seeder
{
    /**
     * Ejecuta el seeder.
     */
    public function run(): void
    {
        $venta = Venta::where(
            'estado_registro',
            'A'
        )
            ->where(
                'estado_venta',
                Venta::BORRADOR
            )
            ->orderBy('id_venta')
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
            ! $venta
            || ! $precioProductoVariante
        ) {

            return;

        }

        DB::transaction(
            function () use (
                $venta,
                $precioProductoVariante
            ): void {

                $cantidad = 1;

                $precioUnitario = (float)
                    $precioProductoVariante
                        ->precio_venta;

                $costoUnitario = (float)
                    $precioProductoVariante
                        ->costo_compra;

                $subtotal =
                    $cantidad * $precioUnitario;

                VentaDetalle::updateOrCreate(

                    [
                        'id_venta' =>
                            $venta->id_venta,

                        'id_producto_variante' =>
                            $precioProductoVariante
                                ->id_producto_variante
                    ],

                    [
                        'cantidad' =>
                            $cantidad,

                        'precio_unitario' =>
                            $precioUnitario,

                        'costo_unitario' =>
                            $costoUnitario,

                        'descuento' =>
                            0,

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

                $venta->update([

                    'subtotal' =>
                        $subtotal,

                    'descuento' =>
                        0,

                    'total' =>
                        $subtotal

                ]);

            }
        );
    }
}