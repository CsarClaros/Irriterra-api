<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Services\Publico\CatalogoPublicoService;
use Illuminate\Http\JsonResponse;


class CatalogoPublicoController
    extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct(

        private readonly
        CatalogoPublicoService
        $catalogoPublicoService

    ) {}


    /*
    |--------------------------------------------------------------------------
    | Catálogo
    |--------------------------------------------------------------------------
    */

    public function index():
    JsonResponse {

        return response()
            ->json([
                'data' =>
                    $this
                        ->catalogoPublicoService
                        ->obtenerCatalogo()
            ]);

    }

}
