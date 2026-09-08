<?php

namespace App\Services\Publico;

use App\Models\Inventario\Categoria;
use App\Models\Inventario\Marca;
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
                    'id_marca',
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
        | Marcas
        |--------------------------------------------------------------------------
        */

        $idsMarcas =
            $variantes
                ->pluck(
                    'id_marca'
                )
                ->filter()
                ->unique()
                ->values();


        $marcas =
            Marca::query()
                ->whereIn(
                    'id_marca',
                    $idsMarcas
                )
                ->where(
                    'estado_registro',
                    'A'
                )
                ->get([
                    'id_marca',
                    'nombre',
                    'sitio_web'
                ])
                ->keyBy(
                    'id_marca'
                );


        /*
        |--------------------------------------------------------------------------
        | Precios públicos
        |--------------------------------------------------------------------------
        |
        | Únicamente exponemos:
        |
        | - precio_venta
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
        | Imágenes generales por producto
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

            /*
            |--------------------------------------------------------------------------
            | Categorías
            |--------------------------------------------------------------------------
            */

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


            /*
            |--------------------------------------------------------------------------
            | Productos
            |--------------------------------------------------------------------------
            */

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
                            $precios,
                            $marcas
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

                                        $precios,

                                        $marcas

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
                    'id_marca',
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
        | Marcas
        |--------------------------------------------------------------------------
        */

        $idsMarcas =
            $variantes
                ->pluck(
                    'id_marca'
                )
                ->filter()
                ->unique()
                ->values();


        $marcas =
            Marca::query()
                ->whereIn(
                    'id_marca',
                    $idsMarcas
                )
                ->where(
                    'estado_registro',
                    'A'
                )
                ->get([
                    'id_marca',
                    'nombre',
                    'sitio_web'
                ])
                ->keyBy(
                    'id_marca'
                );


        /*
        |--------------------------------------------------------------------------
        | Precios públicos
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

                    $precios,

                    $marcas

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
        Collection $precios,
        Collection $marcas
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
                        $precios,
                        $marcas
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
                                        ),

                                    $marcas

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
        | Producto público
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


            /*
             * Campo legado.
             *
             * Se conserva temporalmente
             * para no afectar otras partes
             * del frontend.
             *
             * La marca real actualmente
             * pertenece a cada variante.
             */

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


            /*
            |--------------------------------------------------------------------------
            | Categoría
            |--------------------------------------------------------------------------
            */

            'categoria' =>
                $categoria

                    ? $this
                    ->transformarCategoria(
                        $categoria
                    )

                    : null,


            /*
            |--------------------------------------------------------------------------
            | Imágenes generales
            |--------------------------------------------------------------------------
            */

            'imagenes' =>
                $imagenesPublicas,


            /*
            |--------------------------------------------------------------------------
            | Variantes
            |--------------------------------------------------------------------------
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
        Collection              $imagenes,
        Collection              $marcas
    ): array
    {

        /*
        |--------------------------------------------------------------------------
        | Marca de la variante
        |--------------------------------------------------------------------------
        */

        $marca =
            $variante
                ->id_marca
            !== null

                ? $marcas
                ->get(
                    $variante
                        ->id_marca
                )

                : null;


        /*
        |--------------------------------------------------------------------------
        | Variante pública
        |--------------------------------------------------------------------------
        */

        return [

            'id_producto_variante' =>
                (int)
                $variante
                    ->id_producto_variante,


            /*
            |--------------------------------------------------------------------------
            | Marca
            |--------------------------------------------------------------------------
            */

            'id_marca' =>
                $variante
                    ->id_marca
                !== null

                    ? (int)
                $variante
                    ->id_marca

                    : null,


            'marca' =>
                $marca instanceof Marca

                    ? $this
                    ->transformarMarca(
                        $marca
                    )

                    : null,


            /*
            |--------------------------------------------------------------------------
            | Información de la variante
            |--------------------------------------------------------------------------
            */

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


            /*
            |--------------------------------------------------------------------------
            | Precio público
            |--------------------------------------------------------------------------
            */

            'precio_venta' =>
                $precio

                    ? (float)
                $precio
                    ->precio_venta

                    : null,


            /*
            |--------------------------------------------------------------------------
            | Imágenes exclusivas de esta variante
            |--------------------------------------------------------------------------
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
    | Transformar marca
    |--------------------------------------------------------------------------
    */

    private function transformarMarca(
        Marca $marca
    ): array
    {

        return [

            'id_marca' =>
                (int)
                $marca
                    ->id_marca,


            'nombre' =>
                $marca
                    ->nombre,


            'sitio_web' =>
                $marca
                    ->sitio_web

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
