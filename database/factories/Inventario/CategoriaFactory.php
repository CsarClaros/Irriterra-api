<?php

namespace Database\Factories\Inventario;

use App\Models\Inventario\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoriaFactory extends Factory
{
    /**
     * Modelo asociado.
     */
    protected $model = Categoria::class;

    /**
     * Estado por defecto.
     */
    public function definition(): array
    {
        return [

            'nombre' => fake()->unique()->words(2, true),

            'descripcion' => fake()->sentence(),

            'observaciones' => fake()->optional()->paragraph(),

            'estado_registro' => 'A'

        ];
    }
}