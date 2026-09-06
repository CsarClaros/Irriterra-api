<?php

namespace App\Services\Publico;

use App\Models\Inventario\Producto;


class SitemapPublicoService
{

    /*
    |--------------------------------------------------------------------------
    | Obtener URLs
    |--------------------------------------------------------------------------
    */

    public function obtenerUrls():
    array
    {

        $siteUrl =
            rtrim(
                config(
                    'seo.site_url'
                ),
                '/'
            );


        /*
        |--------------------------------------------------------------------------
        | Páginas estáticas
        |--------------------------------------------------------------------------
        */

        $urls = [

            [
                'loc' =>
                    $siteUrl
                    . '/'
            ],

            [
                'loc' =>
                    $siteUrl
                    . '/productos'
            ],

            [
                'loc' =>
                    $siteUrl
                    . '/empresa'
            ],

            [
                'loc' =>
                    $siteUrl
                    . '/contactos'
            ]

        ];


        /*
        |--------------------------------------------------------------------------
        | Productos públicos
        |--------------------------------------------------------------------------
        */

        $productos =
            Producto::query()
                ->where(
                    'estado_registro',
                    'A'
                )
                ->orderBy(
                    'id_producto'
                )
                ->get([
                    'id_producto',
                    'updated_at'
                ]);


        /*
        |--------------------------------------------------------------------------
        | URLs de productos
        |--------------------------------------------------------------------------
        */

        foreach (
            $productos
            as $producto
        ) {

            $url = [

                'loc' =>
                    $siteUrl
                    . '/productos/'
                    . $producto
                        ->id_producto

            ];


            /*
             * lastmod solamente cuando
             * tenemos una fecha real.
             */

            if (
                $producto
                    ->updated_at
            ) {

                $url['lastmod'] =
                    $producto
                        ->updated_at
                        ->toAtomString();

            }


            $urls[] =
                $url;

        }


        return $urls;

    }

}
