<?php

namespace Database\Seeders\Inventario;

use App\Models\Inventario\PrecioProductoVariante;
use App\Models\Inventario\ProductoVariante;
use Illuminate\Database\Seeder;

class PrecioProductoVarianteSeeder extends Seeder
{
    /**
     * Ejecuta el seeder.
     */
    public function run(): void
    {
        $productoVariante =
            ProductoVariante::where(
                'estado_registro',
                'A'
            )
                ->orderBy(
                    'id_producto_variante'
                )
                ->first();

        if (! $productoVariante) {

            return;

        }

        PrecioProductoVariante::updateOrCreate(

            [
                'id_producto_variante' =>
                    $productoVariante
                        ->id_producto_variante
            ],

            [
                'costo_compra' =>
                    100,

                'precio_minimo' =>
                    120,

                'precio_venta' =>
                    150,

                'fecha_vigencia' =>
                    now(),

                'observaciones' =>
                    'Configuración inicial de precios generada desde el seeder.',

                'estado_registro' =>
                    'A',

                'usuario_creacion' =>
                    null,

                'usuario_modificacion' =>
                    null
            ]

        );
    }
}