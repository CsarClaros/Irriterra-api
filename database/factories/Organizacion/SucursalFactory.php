<?php

namespace Database\Factories\Organizacion;

use App\Models\Organizacion\Empresa;
use App\Models\Organizacion\Sucursal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * -------------------------------------------------------------------------
 * Factory: Sucursal
 * -------------------------------------------------------------------------
 *
 * Genera sucursales de prueba.
 *
 * @extends Factory<Sucursal>
 */
class SucursalFactory extends Factory
{
    /**
     * Modelo asociado.
     *
     * @var class-string<Sucursal>
     */
    protected $model = Sucursal::class;

    /**
     * Define el estado por defecto.
     *
     * @return array<string,mixed>
     */
    public function definition(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Empresa
            |--------------------------------------------------------------------------
            */

            'id_empresa' => Empresa::factory(),

            /*
            |--------------------------------------------------------------------------
            | Información General
            |--------------------------------------------------------------------------
            */

            'codigo' => strtoupper(fake()->unique()->bothify('SC-###')),

            'nombre' => fake()->city() . ' Centro',

            /*
            |--------------------------------------------------------------------------
            | Ubicación
            |--------------------------------------------------------------------------
            */

            'departamento' => fake()->state(),

            'ciudad' => fake()->city(),

            'direccion' => fake()->address(),

            /*
            |--------------------------------------------------------------------------
            | Contacto
            |--------------------------------------------------------------------------
            */

            'telefono' => fake()->phoneNumber(),

            'correo' => fake()->companyEmail(),

            /*
            |--------------------------------------------------------------------------
            | Geolocalización
            |--------------------------------------------------------------------------
            */

            'latitud' => fake()->latitude(),

            'longitud' => fake()->longitude(),

            /*
            |--------------------------------------------------------------------------
            | Otros
            |--------------------------------------------------------------------------
            */

            'observaciones' => fake()->optional()->sentence(),

            'estado_registro' => 'A',

        ];
    }
}