<?php

namespace Database\Seeders\Inventario;

use App\Models\Inventario\ProductoVariante;
use App\Models\Inventario\StockSucursal;
use App\Models\Organizacion\Sucursal;
use Illuminate\Database\Seeder;

class StockSucursalSeeder extends Seeder
{
    /**
     * Ejecuta el seeder.
     */
    public function run(): void
    {
        $sucursal = Sucursal::where(
            'estado_registro',
            'A'
        )
            ->orderBy('id_sucursal')
            ->first();

        $productoVariante = ProductoVariante::where(
            'estado_registro',
            'A'
        )
            ->orderBy('id_producto_variante')
            ->first();

        if (! $sucursal || ! $productoVariante) {

            return;

        }

        StockSucursal::updateOrCreate(

            [
                'id_sucursal' => $sucursal->id_sucursal,

                'id_producto_variante' =>
                    $productoVariante->id_producto_variante
            ],

            [
                'stock_actual' => 0,

                'stock_minimo' => 2,

                'stock_maximo' => 20,

                'ubicacion_almacen' => 'Almacén principal',

                'observaciones' =>
                    'Registro inicial generado desde el seeder.',

                'estado_registro' => 'A',

                'usuario_creacion' => null,

                'usuario_modificacion' => null
            ]

        );
    }
}