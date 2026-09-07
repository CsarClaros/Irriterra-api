<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventario\StoreMarcaRequest;
use App\Http\Requests\Inventario\UpdateMarcaRequest;
use App\Http\Resources\Inventario\MarcaCollection;
use App\Http\Resources\Inventario\MarcaResource;
use App\Models\Inventario\Marca;
use App\Services\Inventario\MarcaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class MarcaController
    extends Controller
{

    public function __construct(
        private readonly MarcaService $service
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
    ): MarcaCollection
    {

        $incluirInactivas =
            $request->boolean(
                'incluir_inactivas'
            );


        return new MarcaCollection(

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
        StoreMarcaRequest $request
    ): JsonResponse
    {

        $marca =
            $this->service
                ->store(
                    $request->validated()
                );


        return response()->json(

            new MarcaResource(
                $marca
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
        Marca $marca
    ): MarcaResource
    {

        return new MarcaResource(

            $this->service
                ->show(
                    $marca
                        ->id_marca
                )

        );

    }


    /*
    |--------------------------------------------------------------------------
    | Actualizar
    |--------------------------------------------------------------------------
    */

    public function update(
        UpdateMarcaRequest $request,
        Marca              $marca
    ): JsonResponse
    {

        $marca =
            $this->service
                ->update(

                    $marca,

                    $request->validated()

                );


        return response()->json(

            new MarcaResource(
                $marca
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
        Marca $marca
    ): JsonResponse
    {

        $this->service
            ->destroy(
                $marca
            );


        return response()->json([

            'message' =>
                'Marca desactivada correctamente.'

        ], 200);

    }


    /*
    |--------------------------------------------------------------------------
    | Reactivar
    |--------------------------------------------------------------------------
    */

    public function reactivate(
        Marca $marca
    ): JsonResponse
    {

        $this->service
            ->reactivate(
                $marca
            );


        return response()->json([

            'message' =>
                'Marca reactivada correctamente.'

        ], 200);

    }

}
