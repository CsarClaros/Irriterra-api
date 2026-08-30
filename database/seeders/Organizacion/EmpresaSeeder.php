<?php

namespace Database\Seeders\Organizacion;

use Illuminate\Database\Seeder;
use App\Models\Organizacion\Empresa;

class EmpresaSeeder extends Seeder
{
    /**
     * Ejecuta el Seeder.
     */
    public function run(): void
    {
        Empresa::updateOrCreate(

            /*
            |--------------------------------------------------------------------------
            | Información General
            |--------------------------------------------------------------------------
            */
            [
                'nit' => '0',
            ],

            [
                'nombre' => 'Irriterra',



                /*
            |--------------------------------------------------------------------------
            | Contacto
            |--------------------------------------------------------------------------
            */

                'telefono' => null,

                'correo' => null,

                'direccion' => null,

                'sitio_web' => null,

                /*
            |--------------------------------------------------------------------------
            | Recursos
            |--------------------------------------------------------------------------
            */

                'logo' => 'empresa/logo.webp',

                /*
            |--------------------------------------------------------------------------
            | Otros
            |--------------------------------------------------------------------------
            */

                'observaciones' => 'Empresa creada automáticamente por el sistema.',

                'estado_registro' => 'A',

            ]

        );
    }
}
