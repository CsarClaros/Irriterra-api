<?php

namespace Database\Seeders\Inventario;

use App\Models\Inventario\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Ejecuta el Seeder.
     */
    public function run(): void
    {
        $categorias = [

            'Bombas',

            'Mangueras',

            'Tuberías',

            'Accesorios',

            'Filtros',

            'Aspersores',

            'Válvulas',

            'Herramientas',

            'Repuestos'

        ];

        foreach ($categorias as $categoria) {

            Categoria::updateOrCreate(

                [

                    'nombre' => $categoria

                ],

                [

                    'descripcion' => null,

                    'observaciones' => null,

                    'estado_registro' => 'A'

                ]

            );

        }
    }
}