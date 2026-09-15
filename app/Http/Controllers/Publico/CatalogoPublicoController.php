<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Services\Publico\CatalogoPublicoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class CatalogoPublicoController
    extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct(

        private readonly CatalogoPublicoService $catalogoPublicoService

    )
    {
    }


    /*
    |--------------------------------------------------------------------------
    | Catálogo
    |--------------------------------------------------------------------------
    */

    public function index(
        Request $request
    ): JsonResponse
    {

        $data =
            $request->validate([

                'page' => [
                    'sometimes',
                    'integer',
                    'min:1'
                ],

                'per_page' => [
                    'sometimes',
                    'integer',
                    'min:1',
                    'max:48'
                ],

                'buscar' => [
                    'nullable',
                    'string',
                    'max:100'
                ],

                'categoria' => [
                    'nullable',
                    'integer',
                    'min:1'
                ]

            ]);


        $buscar =
            isset(
                $data['buscar']
            )
                ? trim(
                $data['buscar']
            )
                : null;


        if (
            $buscar === ''
        ) {

            $buscar = null;

        }


        return response()
            ->json([

                'data' =>
                    $this
                        ->catalogoPublicoService
                        ->obtenerCatalogo(

                            pagina: (int)(
                                $data['page']
                                ?? 1
                            ),

                            porPagina: (int)(
                                $data['per_page']
                                ?? 12
                            ),

                            buscar: $buscar,

                            idCategoria: isset(
                                $data['categoria']
                            )
                                ? (int)
                                $data['categoria']
                                : null

                        )

            ]);

    }


    /*
    |--------------------------------------------------------------------------
    | Categorías
    |--------------------------------------------------------------------------
    */

    public function categorias():
    JsonResponse
    {

        return response()
            ->json([

                'data' =>
                    $this
                        ->catalogoPublicoService
                        ->obtenerCategorias()

            ]);

    }


    /*
    |--------------------------------------------------------------------------
    | Producto
    |--------------------------------------------------------------------------
    */

    public function show(
        int $id
    ): JsonResponse
    {

        return response()
            ->json([

                'data' =>
                    $this
                        ->catalogoPublicoService
                        ->obtenerProducto(
                            $id
                        )

            ]);

    }

}
