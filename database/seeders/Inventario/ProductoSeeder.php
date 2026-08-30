<?php

namespace Database\Seeders\Inventario;

use App\Models\Inventario\Categoria;
use App\Models\Inventario\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    /**
     * Ejecuta el seeder.
     */
    public function run(): void
    {
        $categoria = Categoria::where('estado_registro', 'A')
            ->orderBy('id_categoria')
            ->first();

        if (! $categoria) {

            return;

        }

        Producto::updateOrCreate(

            [
                'nombre' => 'Producto de prueba',
                'modelo' => 'MOD-001'
            ],

            [
                'id_categoria' => $categoria->id_categoria,
                'marca' => 'Müller',
                'descripcion' => 'Producto inicial para pruebas del módulo de inventario.',
                'catalogo_pdf' => null,
                'observaciones' => 'Registro generado desde el seeder.',
                'estado_registro' => 'A',
                'usuario_creacion' => null,
                'usuario_modificacion' => null
            ]

        );
    }
}