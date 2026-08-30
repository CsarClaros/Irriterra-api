<?php

namespace Database\Seeders\Seguridad;

use App\Models\Organizacion\Sucursal;
use App\Models\Seguridad\Rol;
use App\Models\Seguridad\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Ejecuta el Seeder.
     */
    public function run(): void
    {
        $rol = Rol::where('nombre', 'Administrador')->first();

        $sucursal = Sucursal::first();

        if (!$rol || !$sucursal) {
            return;
        }

        Usuario::updateOrCreate(

            [
                'usuario' => 'admin'
            ],

            [

                /*
                |--------------------------------------------------------------------------
                | Relaciones
                |--------------------------------------------------------------------------
                */

                'id_rol' => $rol->id_rol,

                'id_sucursal' => $sucursal->id_sucursal,

                /*
                |--------------------------------------------------------------------------
                | Identificación
                |--------------------------------------------------------------------------
                */

                'ci' => '00000000',

                // 'usuario' => 'admin',

                'password' => Hash::make('00000000'),

                /*
                |--------------------------------------------------------------------------
                | Datos personales
                |--------------------------------------------------------------------------
                */

                'nombre' => 'Administrador',

                'apellido_paterno' => 'Sistema',

                'apellido_materno' => '',

                'correo' => 'admin@irriterra.com',

                'telefono' => null,

                'direccion' => null,

                /*
                |--------------------------------------------------------------------------
                | Acceso
                |--------------------------------------------------------------------------
                */

                // 'password' => Hash::make('00000000'),

                /*
                |--------------------------------------------------------------------------
                | Seguridad
                |--------------------------------------------------------------------------
                */

                'foto' => null,

                'ultimo_acceso' => null,

                'intentos_fallidos' => 0,

                'bloqueado_hasta' => null,

                /*
                |--------------------------------------------------------------------------
                | Estado
                |--------------------------------------------------------------------------
                */

                'estado_registro' => 'A'

            ]

        );
    }
}