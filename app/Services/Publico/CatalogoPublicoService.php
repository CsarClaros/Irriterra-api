<?php

namespace App\Services\Publico;

use App\Models\Inventario\Categoria;
use App\Models\Inventario\PrecioProductoVariante;
use App\Models\Inventario\Producto;
use App\Models\Inventario\ProductoImagen;
use App\Models\Inventario\ProductoVariante;


class CatalogoPublicoService
{

    /*
    |--------------------------------------------------------------------------
    | Obtener catálogo público
    |--------------------------------------------------------------------------
    */

    public function obtenerCatalogo():
    array {

        /*
        |--------------------------------------------------------------------------
        | Categorías
        |--------------------------------------------------------------------------
        */

        $categorias =
            Categoria::query()
                ->where(
                    'estado_registro',
                    'A'
                )
                ->orderBy(
                    'nombre'
                )
                ->get([
                    'id_categoria',
                    'nombre'
                ]);


        /*
        |--------------------------------------------------------------------------
        | Productos
        |--------------------------------------------------------------------------
        */

        $productos =
            Producto::query()
                ->where(
                    'estado_registro',
                    'A'
                )
                ->orderBy(
                    'nombre'
                )
                ->get([
                    'id_producto',
                    'id_categoria',
                    'nombre',
                    'marca',
                    'modelo',
                    'descripcion',
                    'catalogo_pdf'
                ]);


        $idsProductos =
            $productos
                ->pluck(
                    'id_producto'
                );


        /*
        |--------------------------------------------------------------------------
        | Variantes
        |--------------------------------------------------------------------------
        */

        $variantes =
            ProductoVariante::query()
                ->whereIn(
                    'id_producto',
                    $idsProductos
                )
                ->where(
                    'estado_registro',
                    'A'
                )
                ->orderBy(
                    'nombre'
                )
                ->get([
                    'id_producto_variante',
                    'id_producto',
                    'nombre',
                    'codigo_comercial',
                    'unidad_medida',
                    'descripcion'
                ]);


        $idsVariantes =
            $variantes
                ->pluck(
                    'id_producto_variante'
                );


        /*
        |--------------------------------------------------------------------------
        | Precios
        |--------------------------------------------------------------------------
        |
        | MUY IMPORTANTE:
        |
        | Solamente consultamos precio_venta.
        |
        | costo_compra y precio_minimo ni siquiera son seleccionados.
        |
        */

        $precios =
            PrecioProductoVariante::query()
                ->whereIn(
                    'id_producto_variante',
                    $idsVariantes
                )
                ->where(
                    'estado_registro',
                    'A'
                )
                ->get([
                    'id_producto_variante',
                    'precio_venta'
                ])
                ->keyBy(
                    'id_producto_variante'
                );


        /*
        |--------------------------------------------------------------------------
        | Imágenes
        |--------------------------------------------------------------------------
        */

        $imagenes =
            ProductoImagen::query()
                ->whereIn(
                    'id_producto',
                    $idsProductos
                )
                ->where(
                    'estado_registro',
                    'A'
                )
                ->orderByDesc(
                    'es_principal'
                )
                ->orderBy(
                    'orden'
                )
                ->get([
                    'id_producto_imagen',
                    'id_producto',
                    'ruta_imagen',
                    'orden',
                    'es_principal'
                ]);


        /*
        |--------------------------------------------------------------------------
        | Agrupaciones
        |--------------------------------------------------------------------------
        */

        $categoriasPorId =
            $categorias
                ->keyBy(
                    'id_categoria'
                );


        $variantesPorProducto =
            $variantes
                ->groupBy(
                    'id_producto'
                );


        $imagenesPorProducto =
            $imagenes
                ->groupBy(
                    'id_producto'
                );


        /*
        |--------------------------------------------------------------------------
        | Respuesta pública
        |--------------------------------------------------------------------------
        */

        return [

            'categorias' =>
                $categorias
                    ->map(
                        function (
                            Categoria $categoria
                        ): array {

                            return [

                                'id_categoria' =>
                                    $categoria
                                        ->id_categoria,

                                'nombre' =>
                                    $categoria
                                        ->nombre

                            ];

                        }
                    )
                    ->values()
                    ->all(),


            'productos' =>
                $productos
                    ->map(
                        function (
                            Producto $producto
                        ) use (
                            $categoriasPorId,
                            $variantesPorProducto,
                            $imagenesPorProducto,
                            $precios
                        ): array {

                            /*
                            |--------------------------------------------------------------------------
                            | Categoría
                            |--------------------------------------------------------------------------
                            */

                            $categoria =
                                $categoriasPorId
                                    ->get(
                                        $producto
                                            ->id_categoria
                                    );


                            /*
                            |--------------------------------------------------------------------------
                            | Variantes públicas
                            |--------------------------------------------------------------------------
                            */

                            $variantesPublicas =
                                $variantesPorProducto
                                    ->get(
                                        $producto
                                            ->id_producto,
                                        collect()
                                    )
                                    ->map(
                                        function (
                                            ProductoVariante $variante
                                        ) use (
                                            $precios
                                        ): array {

                                            $precio =
                                                $precios
                                                    ->get(
                                                        $variante
                                                            ->id_producto_variante
                                                    );


                                            return [

                                                'id_producto_variante' =>
                                                    $variante
                                                        ->id_producto_variante,

                                                'nombre' =>
                                                    $variante
                                                        ->nombre,

                                                'codigo_comercial' =>
                                                    $variante
                                                        ->codigo_comercial,

                                                'unidad_medida' =>
                                                    $variante
                                                        ->unidad_medida,

                                                'descripcion' =>
                                                    $variante
                                                        ->descripcion,

                                                'precio_venta' =>
                                                    $precio
                                                        ? (float) $precio
                                                        ->precio_venta
                                                        : null

                                            ];

                                        }
                                    )
                                    ->values()
                                    ->all();


                            /*
                            |--------------------------------------------------------------------------
                            | Imágenes públicas
                            |--------------------------------------------------------------------------
                            */

                            $imagenesPublicas =
                                $imagenesPorProducto
                                    ->get(
                                        $producto
                                            ->id_producto,
                                        collect()
                                    )
                                    ->map(
                                        function (
                                            ProductoImagen $imagen
                                        ): array {

                                            return [

                                                'id_producto_imagen' =>
                                                    $imagen
                                                        ->id_producto_imagen,

                                                'ruta_imagen' =>
                                                    $imagen
                                                        ->ruta_imagen,

                                                'orden' =>
                                                    (int) $imagen
                                                ->orden,
                                                'es_principal' => (bool) $imagen
                                                ->es_principal
                                            ];

                                        }
                                    )
                                    ->values()
                                    ->all();


                            return [

                                'id_producto' =>
                                    $producto
                                        ->id_producto,

                                'id_categoria' =>
                                    $producto
                                        ->id_categoria,

                                'nombre' =>
                                    $producto
                                        ->nombre,

                                'marca' =>
                                    $producto
                                        ->marca,

                                'modelo' =>
                                    $producto
                                        ->modelo,

                                'descripcion' =>
                                    $producto
                                        ->descripcion,

                                'catalogo_pdf' =>
                                    $producto
                                        ->catalogo_pdf,


                                'categoria' =>
                                    $categoria
                                        ? [

                                        'id_categoria' =>
                                            $categoria
                                                ->id_categoria,

                                        'nombre' =>
                                            $categoria
                                                ->nombre

                                    ]
                                        : null,


                                'imagenes' =>
                                    $imagenesPublicas,


                                'variantes' =>
                                    $variantesPublicas

                            ];

                        }
                    )
                    ->values()
                    ->all()

        ];

    }

}
