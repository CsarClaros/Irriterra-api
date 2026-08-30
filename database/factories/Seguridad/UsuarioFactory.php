<?php

namespace Database\Factories\Seguridad;

use App\Models\Organizacion\Sucursal;
use App\Models\Seguridad\Rol;
use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UsuarioFactory extends Factory
{
    /**
     * Modelo asociado.
     */
    protected $model = Usuario::class;

    /**
     * Estado por defecto.
     */
    public function definition(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Relaciones
            |--------------------------------------------------------------------------
            */

            'id_rol' => Rol::inRandomOrder()->value('id_rol'),

            'id_sucursal' => Sucursal::inRandomOrder()->value('id_sucursal'),

            /*
            |--------------------------------------------------------------------------
            | Acceso
            |--------------------------------------------------------------------------
            */

            'ci' => fake()->unique()->numerify('########'),

            'usuario' => fake()->unique()->userName(),

            'password' => Hash::make('123456'),

            /*
            |--------------------------------------------------------------------------
            | Datos personales
            |--------------------------------------------------------------------------
            */

            'nombre' => fake()->firstName(),

            'apellido_paterno' => fake()->lastName(),

            'apellido_materno' => fake()->lastName(),

            'correo' => fake()->unique()->safeEmail(),

            'telefono' => fake()->phoneNumber(),

            'direccion' => fake()->address(),

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

            'estado_registro' => 'A',

            /*
            |--------------------------------------------------------------------------
            | Auditoría
            |--------------------------------------------------------------------------
            */

            'usuario_creacion' => null,

            'usuario_modificacion' => null

        ];
    }
}