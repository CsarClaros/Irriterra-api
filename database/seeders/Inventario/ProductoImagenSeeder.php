<?php

namespace Database\Seeders\Inventario;

use App\Models\Inventario\Producto;
use App\Models\Inventario\ProductoImagen;
use Illuminate\Database\Seeder;

class ProductoImagenSeeder extends Seeder
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

        ProductoImagen::updateOrCreate(

            [
                'id_producto' => $producto->id_producto,
                'ruta_imagen' => 'productos/default/main.webp'
            ],

            [
                'texto_alternativo' => $producto->nombre,
                'orden' => 1,
                'es_principal' => true,
                'observaciones' =>
                    'Imagen inicial generada desde el seeder.',
                'estado_registro' => 'A',
                'usuario_creacion' => null,
                'usuario_modificacion' => null
            ]

        );

        ProductoImagen::updateOrCreate(

            [
                'id_producto' => $producto->id_producto,
                'ruta_imagen' => 'productos/default/1.webp'
            ],

            [
                'texto_alternativo' =>
                    $producto->nombre . ' - Vista adicional',
                'orden' => 2,
                'es_principal' => false,
                'observaciones' =>
                    'Imagen secundaria generada desde el seeder.',
                'estado_registro' => 'A',
                'usuario_creacion' => null,
                'usuario_modificacion' => null
            ]

        );
    }
}