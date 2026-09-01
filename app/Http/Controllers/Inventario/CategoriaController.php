<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventario\StoreCategoriaRequest;
use App\Http\Requests\Inventario\UpdateCategoriaRequest;
use App\Http\Resources\Inventario\CategoriaCollection;
use App\Http\Resources\Inventario\CategoriaResource;
use App\Models\Inventario\Categoria;
use App\Services\Inventario\CategoriaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class CategoriaController
    extends Controller
{

    public function __construct(
        private readonly CategoriaService $service
    )
    {
    }


    /*
    |--------------------------------------------------------------------------
    | Listar
    |--------------------------------------------------------------------------
    */

    public function index(
        Request $request
    ): CategoriaCollection
    {

        $incluirInactivas =
            $request->boolean(
                'incluir_inactivas'
            );


        return new CategoriaCollection(

            $this->service
                ->index(
                    $incluirInactivas
                )

        );

    }


    /*
    |--------------------------------------------------------------------------
    | Crear
    |--------------------------------------------------------------------------
    */

    public function store(
        StoreCategoriaRequest $request
    ): JsonResponse
    {

        $categoria =
            $this->service
                ->store(
                    $request->validated()
                );


        return response()->json(

            new CategoriaResource(
                $categoria
            ),

            201

        );

    }


    /*
    |--------------------------------------------------------------------------
    | Mostrar
    |--------------------------------------------------------------------------
    */

    public function show(
        Categoria $categoria
    ): CategoriaResource
    {

        return new CategoriaResource(

            $this->service
                ->show(
                    $categoria
                        ->id_categoria
                )

        );

    }


    /*
    |--------------------------------------------------------------------------
    | Actualizar
    |--------------------------------------------------------------------------
    */

    public function update(
        UpdateCategoriaRequest $request,
        Categoria              $categoria
    ): JsonResponse
    {

        $categoria =
            $this->service
                ->update(

                    $categoria,

                    $request->validated()

                );


        return response()->json(

            new CategoriaResource(
                $categoria
            ),

            200

        );

    }


    /*
    |--------------------------------------------------------------------------
    | Desactivar
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Categoria $categoria
    ): JsonResponse
    {

        $this->service
            ->destroy(
                $categoria
            );


        return response()->json([

            'message' =>
                'Categoría desactivada correctamente.'

        ], 200);

    }


    /*
    |--------------------------------------------------------------------------
    | Reactivar
    |--------------------------------------------------------------------------
    */

    public function reactivate(
        Categoria $categoria
    ): JsonResponse
    {

        $this->service
            ->reactivate(
                $categoria
            );


        return response()->json([

            'message' =>
                'Categoría reactivada correctamente.'

        ], 200);

    }

}
