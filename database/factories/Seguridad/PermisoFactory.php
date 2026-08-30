<?php

namespace Database\Factories\Seguridad;

use App\Models\Seguridad\Permiso;
use Illuminate\Database\Eloquent\Factories\Factory;

class PermisoFactory extends Factory
{
    /**
     * Modelo asociado al Factory.
     *
     * @var string
     */
    protected $model = Permiso::class;

    /**
     * Estado por defecto.
     */
    public function definition(): array
    {
        return [

            'nombre' => fake()->unique()->bothify('modulo.????'),

            'descripcion' => fake()->sentence(),

            'estado_registro' => 'A',

            'usuario_creacion' => null,

            'usuario_modificacion' => null

        ];
    }
}