<?php

namespace Database\Seeders;

use Database\Seeders\Inventario\CategoriaSeeder;
use Database\Seeders\Inventario\MarcaSeeder;
use Database\Seeders\Organizacion\EmpresaSeeder;
use Database\Seeders\Organizacion\SucursalSeeder;
use Database\Seeders\Seguridad\PermisoSeeder;
use Database\Seeders\Seguridad\RolPermisoSeeder;
use Database\Seeders\Seguridad\RolSeeder;
use Illuminate\Database\Seeder;


class ProductionSeeder extends Seeder
{

    /**
     * Inicializa únicamente los datos
     * estructurales requeridos por Irriterra.
     */
    public function run(): void
    {

        $this->call([

            /*
            |--------------------------------------------------------------------------
            | Organización
            |--------------------------------------------------------------------------
            */

            EmpresaSeeder::class,

            SucursalSeeder::class,


            /*
            |--------------------------------------------------------------------------
            | Seguridad
            |--------------------------------------------------------------------------
            */

            RolSeeder::class,

            PermisoSeeder::class,

            RolPermisoSeeder::class,


            /*
            |--------------------------------------------------------------------------
            | Catálogo
            |--------------------------------------------------------------------------
            */

            CategoriaSeeder::class,

            MarcaSeeder::class,

        ]);

    }

}
