<?php

namespace Database\Seeders;

use Database\Seeders\Compras\CompraDetalleSeeder;
use Database\Seeders\Compras\CompraSeeder;
use Database\Seeders\Compras\ProveedorSeeder;
use Database\Seeders\Inventario\MovimientoInventarioSeeder;
use Database\Seeders\Inventario\PrecioProductoVarianteSeeder;
use Database\Seeders\Inventario\ProductoImagenSeeder;
use Database\Seeders\Inventario\ProductoSeeder;
use Database\Seeders\Inventario\ProductoVarianteSeeder;
use Database\Seeders\Inventario\StockSucursalSeeder;
use Database\Seeders\Seguridad\UsuarioSeeder;
use Database\Seeders\Transferencias\TransferenciaInventarioDetalleSeeder;
use Database\Seeders\Transferencias\TransferenciaInventarioSeeder;
use Database\Seeders\Ventas\ClienteSeeder;
use Database\Seeders\Ventas\VentaDetalleSeeder;
use Database\Seeders\Ventas\VentaSeeder;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{

    /**
     * Inicializa datos para desarrollo
     * y pruebas locales.
     */
    public function run(): void
    {

        /*
        |--------------------------------------------------------------------------
        | Datos estructurales
        |--------------------------------------------------------------------------
        */

        $this->call(
            ProductionSeeder::class
        );


        /*
        |--------------------------------------------------------------------------
        | Usuario de desarrollo
        |--------------------------------------------------------------------------
        */

        $this->call(
            UsuarioSeeder::class
        );


        /*
        |--------------------------------------------------------------------------
        | Inventario de prueba
        |--------------------------------------------------------------------------
        */

        $this->call([

            ProductoSeeder::class,

            ProductoVarianteSeeder::class,

            ProductoImagenSeeder::class,

            StockSucursalSeeder::class,

            MovimientoInventarioSeeder::class,

            PrecioProductoVarianteSeeder::class,

        ]);


        /*
        |--------------------------------------------------------------------------
        | Ventas de prueba
        |--------------------------------------------------------------------------
        */

        $this->call([

            ClienteSeeder::class,

            VentaSeeder::class,

            VentaDetalleSeeder::class,

        ]);


        /*
        |--------------------------------------------------------------------------
        | Compras de prueba
        |--------------------------------------------------------------------------
        */

        $this->call([

            ProveedorSeeder::class,

            CompraSeeder::class,

            CompraDetalleSeeder::class,

        ]);


        /*
        |--------------------------------------------------------------------------
        | Transferencias de prueba
        |--------------------------------------------------------------------------
        */

        $this->call([

            TransferenciaInventarioSeeder::class,

            TransferenciaInventarioDetalleSeeder::class,

        ]);

    }

}
