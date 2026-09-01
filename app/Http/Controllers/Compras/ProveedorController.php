<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Http\Requests\Compras\StoreProveedorRequest;
use App\Http\Requests\Compras\UpdateProveedorRequest;
use App\Http\Resources\Compras\ProveedorCollection;
use App\Http\Resources\Compras\ProveedorResource;
use App\Models\Compras\Proveedor;
use App\Services\Compras\ProveedorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ProveedorController
    extends Controller
{

    public function __construct(
        private readonly ProveedorService $service
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
    ): ProveedorCollection
    {

        $incluirInactivos =
            $request->boolean(
                'incluir_inactivos'
            );


        return new ProveedorCollection(

            $this->service
                ->index(
                    $incluirInactivos
                )

        );

    }


    /*
    |--------------------------------------------------------------------------
    | Crear
    |--------------------------------------------------------------------------
    */

    public function store(
        StoreProveedorRequest $request
    ): JsonResponse
    {

        $proveedor =
            $this->service
                ->store(
                    $request->validated()
                );


        return response()->json(

            new ProveedorResource(
                $proveedor
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
        Proveedor $proveedor
    ): ProveedorResource
    {

        return new ProveedorResource(

            $this->service
                ->show(
                    $proveedor
                        ->id_proveedor
                )

        );

    }


    /*
    |--------------------------------------------------------------------------
    | Actualizar
    |--------------------------------------------------------------------------
    */

    public function update(
        UpdateProveedorRequest $request,
        Proveedor              $proveedor
    ): JsonResponse
    {

        $proveedor =
            $this->service
                ->update(

                    $proveedor,

                    $request->validated()

                );


        return response()->json(

            new ProveedorResource(
                $proveedor
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
        Proveedor $proveedor
    ): JsonResponse
    {

        $this->service
            ->destroy(
                $proveedor
            );


        return response()->json([

            'message' =>
                'Proveedor desactivado correctamente.'

        ], 200);

    }


    /*
    |--------------------------------------------------------------------------
    | Reactivar
    |--------------------------------------------------------------------------
    */

    public function reactivate(
        Proveedor $proveedor
    ): JsonResponse
    {

        $this->service
            ->reactivate(
                $proveedor
            );


        return response()->json([

            'message' =>
                'Proveedor reactivado correctamente.'

        ], 200);

    }

}
