<?php

namespace Database\Seeders;

use Database\Seeders\Compras\CompraDetalleSeeder;
use Database\Seeders\Compras\CompraSeeder;
use Database\Seeders\Compras\ProveedorSeeder;
use Database\Seeders\Inventario\CategoriaSeeder;
use Database\Seeders\Inventario\MovimientoInventarioSeeder;
use Database\Seeders\Inventario\PrecioProductoVarianteSeeder;
use Database\Seeders\Inventario\ProductoImagenSeeder;
use Database\Seeders\Inventario\ProductoSeeder;
use Database\Seeders\Inventario\ProductoVarianteSeeder;
use Database\Seeders\Inventario\StockSucursalSeeder;
use Illuminate\Database\Seeder;

use Database\Seeders\Organizacion\EmpresaSeeder;
use Database\Seeders\Organizacion\SucursalSeeder;
use Database\Seeders\Seguridad\PermisoSeeder;
use Database\Seeders\Seguridad\RolPermisoSeeder;
use Database\Seeders\Seguridad\RolSeeder;
use Database\Seeders\Seguridad\UsuarioSeeder;
use Database\Seeders\Transferencias\TransferenciaInventarioDetalleSeeder;
use Database\Seeders\Transferencias\TransferenciaInventarioSeeder;
use Database\Seeders\Ventas\ClienteSeeder;
use Database\Seeders\Ventas\VentaDetalleSeeder;
use Database\Seeders\Ventas\VentaSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed de la Base de Datos.
     */
    public function run(): void
    {
        $this->call([

            EmpresaSeeder::class,

            SucursalSeeder::class,

            RolSeeder::class,

            PermisoSeeder::class,

            RolPermisoSeeder::class,

            UsuarioSeeder::class,

            CategoriaSeeder::class,

            ProductoSeeder::class,

            ProductoVarianteSeeder::class,

            ProductoImagenSeeder::class,

            StockSucursalSeeder::class,

            MovimientoInventarioSeeder::class,

            PrecioProductoVarianteSeeder::class,

            TransferenciaInventarioSeeder::class,

            TransferenciaInventarioDetalleSeeder::class,

            ClienteSeeder::class,

            VentaSeeder::class,

            VentaDetalleSeeder::class,

            ProveedorSeeder::class,

            CompraSeeder::class,

            CompraDetalleSeeder::class,



        ]);
    }
}