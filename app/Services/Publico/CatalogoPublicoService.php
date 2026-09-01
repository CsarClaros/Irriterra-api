<?php

namespace App\Services\Publico;

use App\Models\Inventario\Categoria;
use App\Models\Inventario\PrecioProductoVariante;
use App\Models\Inventario\Producto;
use App\Models\Inventario\ProductoImagen;
use App\Models\Inventario\ProductoVariante;
use Illuminate\Support\Collection;


class CatalogoPublicoService
{

    /*
    |--------------------------------------------------------------------------
    | Obtener catálogo público
    |--------------------------------------------------------------------------
    */

    public function obtenerCatalogo():
    array
    {

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
        | Precios públicos
        |--------------------------------------------------------------------------
        |
        | Únicamente exponemos precio_venta.
        |
        | Nunca:
        |
        | - costo_compra
        | - precio_minimo
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
                    'id_producto_variante',
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


        /*
        |--------------------------------------------------------------------------
        | Imágenes generales
        |--------------------------------------------------------------------------
        |
        | id_producto_variante = NULL
        |
        */

        $imagenesGeneralesPorProducto =
            $imagenes
                ->filter(
                    function (
                        ProductoImagen $imagen
                    ): bool {

                        return
                            $imagen
                                ->id_producto_variante
                            === null;

                    }
                )
                ->groupBy(
                    'id_producto'
                );


        /*
        |--------------------------------------------------------------------------
        | Imágenes específicas por variante
        |--------------------------------------------------------------------------
        */

        $imagenesPorVariante =
            $imagenes
                ->filter(
                    function (
                        ProductoImagen $imagen
                    ): bool {

                        return
                            $imagen
                                ->id_producto_variante
                            !== null;

                    }
                )
                ->groupBy(
                    'id_producto_variante'
                );


        /*
        |--------------------------------------------------------------------------
        | Respuesta
        |--------------------------------------------------------------------------
        */

        return [

            'categorias' =>
                $categorias
                    ->map(
                        function (
                            Categoria $categoria
                        ): array {

                            return
                                $this
                                    ->transformarCategoria(
                                        $categoria
                                    );

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
                            $imagenesGeneralesPorProducto,
                            $imagenesPorVariante,
                            $precios
                        ): array {

                            return
                                $this
                                    ->transformarProducto(

                                        $producto,

                                        $categoriasPorId
                                            ->get(
                                                $producto
                                                    ->id_categoria
                                            ),

                                        $variantesPorProducto
                                            ->get(
                                                $producto
                                                    ->id_producto,
                                                collect()
                                            ),

                                        $imagenesGeneralesPorProducto
                                            ->get(
                                                $producto
                                                    ->id_producto,
                                                collect()
                                            ),

                                        $imagenesPorVariante,

                                        $precios

                                    );

                        }
                    )
                    ->values()
                    ->all()

        ];

    }


    /*
    |--------------------------------------------------------------------------
    | Obtener producto público
    |--------------------------------------------------------------------------
    */

    public function obtenerProducto(
        int $id
    ): array
    {

        /*
        |--------------------------------------------------------------------------
        | Producto
        |--------------------------------------------------------------------------
        */

        $producto =
            Producto::query()
                ->where(
                    'estado_registro',
                    'A'
                )
                ->findOrFail(
                    $id,
                    [
                        'id_producto',
                        'id_categoria',
                        'nombre',
                        'marca',
                        'modelo',
                        'descripcion',
                        'catalogo_pdf'
                    ]
                );


        /*
        |--------------------------------------------------------------------------
        | Categoría
        |--------------------------------------------------------------------------
        */

        $categoria =
            Categoria::query()
                ->where(
                    'id_categoria',
                    $producto
                        ->id_categoria
                )
                ->where(
                    'estado_registro',
                    'A'
                )
                ->first([
                    'id_categoria',
                    'nombre'
                ]);


        /*
        |--------------------------------------------------------------------------
        | Variantes
        |--------------------------------------------------------------------------
        */

        $variantes =
            ProductoVariante::query()
                ->where(
                    'id_producto',
                    $producto
                        ->id_producto
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
                ->where(
                    'id_producto',
                    $producto
                        ->id_producto
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
                    'id_producto_variante',
                    'ruta_imagen',
                    'orden',
                    'es_principal'
                ]);


        /*
        |--------------------------------------------------------------------------
        | Imágenes generales
        |--------------------------------------------------------------------------
        */

        $imagenesGenerales =
            $imagenes
                ->filter(
                    function (
                        ProductoImagen $imagen
                    ): bool {

                        return
                            $imagen
                                ->id_producto_variante
                            === null;

                    }
                )
                ->values();


        /*
        |--------------------------------------------------------------------------
        | Imágenes por variante
        |--------------------------------------------------------------------------
        */

        $imagenesPorVariante =
            $imagenes
                ->filter(
                    function (
                        ProductoImagen $imagen
                    ): bool {

                        return
                            $imagen
                                ->id_producto_variante
                            !== null;

                    }
                )
                ->groupBy(
                    'id_producto_variante'
                );


        /*
        |--------------------------------------------------------------------------
        | Respuesta
        |--------------------------------------------------------------------------
        */

        return
            $this
                ->transformarProducto(

                    $producto,

                    $categoria,

                    $variantes,

                    $imagenesGenerales,

                    $imagenesPorVariante,

                    $precios

                );

    }


    /*
    |--------------------------------------------------------------------------
    | Transformar categoría
    |--------------------------------------------------------------------------
    */

    private function transformarCategoria(
        Categoria $categoria
    ): array
    {

        return [

            'id_categoria' =>
                (int)
                $categoria
                    ->id_categoria,

            'nombre' =>
                $categoria
                    ->nombre

        ];

    }


    /*
    |--------------------------------------------------------------------------
    | Transformar producto
    |--------------------------------------------------------------------------
    */

    private function transformarProducto(
        Producto   $producto,
        ?Categoria $categoria,
        Collection $variantes,
        Collection $imagenesGenerales,
        Collection $imagenesPorVariante,
        Collection $precios
    ): array
    {

        /*
        |--------------------------------------------------------------------------
        | Variantes públicas
        |--------------------------------------------------------------------------
        */

        $variantesPublicas =
            $variantes
                ->map(
                    function (
                        ProductoVariante $variante
                    ) use (
                        $imagenesPorVariante,
                        $precios
                    ): array {

                        return
                            $this
                                ->transformarVariante(

                                    $variante,

                                    $precios
                                        ->get(
                                            $variante
                                                ->id_producto_variante
                                        ),

                                    $imagenesPorVariante
                                        ->get(
                                            $variante
                                                ->id_producto_variante,
                                            collect()
                                        )

                                );

                    }
                )
                ->values()
                ->all();


        /*
        |--------------------------------------------------------------------------
        | Imágenes generales públicas
        |--------------------------------------------------------------------------
        */

        $imagenesPublicas =
            $imagenesGenerales
                ->map(
                    function (
                        ProductoImagen $imagen
                    ): array {

                        return
                            $this
                                ->transformarImagen(
                                    $imagen
                                );

                    }
                )
                ->values()
                ->all();


        /*
        |--------------------------------------------------------------------------
        | Producto
        |--------------------------------------------------------------------------
        */

        return [

            'id_producto' =>
                (int)
                $producto
                    ->id_producto,

            'id_categoria' =>
                (int)
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
                    ? $this
                    ->transformarCategoria(
                        $categoria
                    )
                    : null,


            /*
             * Solamente imágenes generales.
             */

            'imagenes' =>
                $imagenesPublicas,


            /*
             * Cada variante contiene ahora
             * sus propias imágenes.
             */

            'variantes' =>
                $variantesPublicas

        ];

    }


    /*
    |--------------------------------------------------------------------------
    | Transformar variante
    |--------------------------------------------------------------------------
    */

    private function transformarVariante(
        ProductoVariante        $variante,
        ?PrecioProductoVariante $precio,
        Collection              $imagenes
    ): array
    {

        return [

            'id_producto_variante' =>
                (int)
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
                    ? (float)
                $precio
                    ->precio_venta
                    : null,


            /*
             * Imágenes exclusivas
             * de esta variante.
             */

            'imagenes' =>
                $imagenes
                    ->map(
                        function (
                            ProductoImagen $imagen
                        ): array {

                            return
                                $this
                                    ->transformarImagen(
                                        $imagen
                                    );

                        }
                    )
                    ->values()
                    ->all()

        ];

    }


    /*
    |--------------------------------------------------------------------------
    | Transformar imagen
    |--------------------------------------------------------------------------
    */

    private function transformarImagen(
        ProductoImagen $imagen
    ): array
    {

        return [

            'id_producto_imagen' =>
                (int)
                $imagen
                    ->id_producto_imagen,

            'ruta_imagen' =>
                $imagen
                    ->ruta_imagen,

            'orden' =>
                (int)
                $imagen
                    ->orden,

            'es_principal' =>
                (bool)
                $imagen
                    ->es_principal

        ];

    }

}
