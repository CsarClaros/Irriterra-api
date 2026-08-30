<?php

namespace Database\Seeders\Inventario;

use App\Models\Inventario\Producto;
use App\Models\Inventario\ProductoVariante;
use Illuminate\Database\Seeder;

class ProductoVarianteSeeder extends Seeder
{
    /**
     * Ejecuta el seeder.
     */
    public function run(): void
    {
        $producto = Producto::where('estado_registro', 'A')
            ->orderBy('id_producto')
            ->first();

        if (! $producto) {

            return;

        }

        ProductoVariante::updateOrCreate(

            [
                'sku' => 'PROD-001-UND'
            ],

            [
                'id_producto' => $producto->id_producto,
                'nombre' => 'Presentación estándar',
                'codigo_comercial' => 'PROD-001',
                'unidad_medida' => 'UND',
                'descripcion' => 'Variante inicial del producto.',
                'observaciones' => 'Registro generado desde el seeder.',
                'estado_registro' => 'A',
                'usuario_creacion' => null,
                'usuario_modificacion' => null
            ]

        );
    }
}