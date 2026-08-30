<?php

namespace Database\Seeders\Transferencias;

use App\Models\Inventario\ProductoVariante;
use App\Models\Transferencias\TransferenciaInventario;
use App\Models\Transferencias\TransferenciaInventarioDetalle;
use Illuminate\Database\Seeder;

class TransferenciaInventarioDetalleSeeder extends Seeder
{
    /**
     * Ejecuta el seeder.
     */
    public function run(): void
    {
        $transferenciaInventario =
            TransferenciaInventario::where(
                'estado_registro',
                'A'
            )
                ->orderBy(
                    'id_transferencia_inventario'
                )
                ->first();

        $productoVariante =
            ProductoVariante::where(
                'estado_registro',
                'A'
            )
                ->orderBy(
                    'id_producto_variante'
                )
                ->first();

        if (
            ! $transferenciaInventario
            || ! $productoVariante
        ) {

            return;

        }

        TransferenciaInventarioDetalle::updateOrCreate(

            [
                'id_transferencia_inventario' =>
                    $transferenciaInventario
                        ->id_transferencia_inventario,

                'id_producto_variante' =>
                    $productoVariante
                        ->id_producto_variante
            ],

            [
                'cantidad' => 1,

                'observaciones' =>
                    'Detalle inicial generado desde el seeder.',

                'estado_registro' => 'A',

                'usuario_creacion' => null,

                'usuario_modificacion' => null
            ]

        );
    }
}