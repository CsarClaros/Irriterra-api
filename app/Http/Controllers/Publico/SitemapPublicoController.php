<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Services\Publico\SitemapPublicoService;
use Illuminate\Http\Response;


class SitemapPublicoController
    extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct(

        private readonly SitemapPublicoService $sitemapPublicoService

    )
    {
    }


    /*
    |--------------------------------------------------------------------------
    | Sitemap
    |--------------------------------------------------------------------------
    */

    public function index():
    Response
    {

        $urls =
            $this
                ->sitemapPublicoService
                ->obtenerUrls();


        return response()
            ->view(
                'seo.sitemap',
                [
                    'urls' =>
                        $urls
                ],
                200,
                [
                    'Content-Type' =>
                        'application/xml; charset=UTF-8',

                    'Cache-Control' =>
                        'public, max-age=3600'
                ]
            );

    }

}
