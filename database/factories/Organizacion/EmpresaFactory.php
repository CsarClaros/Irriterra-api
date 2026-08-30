<?php

namespace Database\Factories\Organizacion;

use App\Models\Organizacion\Empresa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * -------------------------------------------------------------------------
 * Factory: Empresa
 * -------------------------------------------------------------------------
 *
 * Genera datos ficticios para pruebas del módulo Empresa.
 *
 * @extends Factory<Empresa>
 */
class EmpresaFactory extends Factory
{
    /**
     * Modelo asociado.
     *
     * @var class-string<Empresa>
     */
    protected $model = Empresa::class;

    /**
     * Define el estado por defecto.
     *
     * @return array<string,mixed>
     */
    public function definition(): array
    {
        return [

            'nombre' => fake()->company(),

            'nit' => fake()->unique()->numerify('##########'),

            'telefono' => fake()->phoneNumber(),

            'correo' => fake()->unique()->companyEmail(),

            'direccion' => fake()->address(),

            'sitio_web' => fake()->url(),

            'logo' => 'empresa/logo.webp',

            'observaciones' => fake()->optional()->sentence(),

            'estado_registro' => 'A',

        ];
    }
}