<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ventas\StoreClienteRequest;
use App\Http\Requests\Ventas\UpdateClienteRequest;
use App\Http\Resources\Ventas\ClienteCollection;
use App\Http\Resources\Ventas\ClienteResource;
use App\Models\Ventas\Cliente;
use App\Services\Ventas\ClienteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ClienteController
    extends Controller
{

    public function __construct(
        private readonly ClienteService $service
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
    ): ClienteCollection
    {

        $incluirInactivos =
            $request->boolean(
                'incluir_inactivos'
            );


        return new ClienteCollection(

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
        StoreClienteRequest $request
    ): JsonResponse
    {

        $cliente =
            $this->service
                ->store(
                    $request->validated()
                );


        return response()->json(

            new ClienteResource(
                $cliente
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
        Cliente $cliente
    ): ClienteResource
    {

        return new ClienteResource(

            $this->service
                ->show(
                    $cliente
                        ->id_cliente
                )

        );

    }


    /*
    |--------------------------------------------------------------------------
    | Actualizar
    |--------------------------------------------------------------------------
    */

    public function update(
        UpdateClienteRequest $request,
        Cliente              $cliente
    ): JsonResponse
    {

        $cliente =
            $this->service
                ->update(

                    $cliente,

                    $request->validated()

                );


        return response()->json(

            new ClienteResource(
                $cliente
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
        Cliente $cliente
    ): JsonResponse
    {

        $this->service
            ->destroy(
                $cliente
            );


        return response()->json([

            'message' =>
                'Cliente desactivado correctamente.'

        ], 200);

    }


    /*
    |--------------------------------------------------------------------------
    | Reactivar
    |--------------------------------------------------------------------------
    */

    public function reactivate(
        Cliente $cliente
    ): JsonResponse
    {

        $this->service
            ->reactivate(
                $cliente
            );


        return response()->json([

            'message' =>
                'Cliente reactivado correctamente.'

        ], 200);

    }

}
