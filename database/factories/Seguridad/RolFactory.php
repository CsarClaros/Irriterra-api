<?php

namespace Database\Factories\Seguridad;

use App\Models\Seguridad\Rol;
use Illuminate\Database\Eloquent\Factories\Factory;

class RolFactory extends Factory
{
    /**
     * Modelo asociado al Factory.
     *
     * @var string
     */
    protected $model = Rol::class;

    /**
     * Estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [

            'nombre' => fake()->unique()->jobTitle(),

            'descripcion' => fake()->sentence(),

            'estado_registro' => 'A',

            'usuario_creacion' => null,

            'usuario_modificacion' => null

        ];
    }
}