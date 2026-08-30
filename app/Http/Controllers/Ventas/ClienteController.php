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

class ClienteController extends Controller
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly ClienteService $service
    ) {
    }

    /**
     * Lista clientes activos.
     */
    public function index(): ClienteCollection
    {
        return new ClienteCollection(

            $this->service->index()

        );
    }

    /**
     * Registra un cliente.
     */
    public function store(
        StoreClienteRequest $request
    ): JsonResponse {

        $cliente =
            $this->service->store(

                $request->validated()

            );

        return response()->json(

            new ClienteResource(
                $cliente
            ),

            201

        );
    }

    /**
     * Muestra un cliente.
     */
    public function show(
        Cliente $cliente
    ): ClienteResource {

        return new ClienteResource(

            $this->service->show(

                $cliente->id_cliente

            )

        );
    }

    /**
     * Actualiza un cliente.
     */
    public function update(
        UpdateClienteRequest $request,
        Cliente $cliente
    ): JsonResponse {

        $cliente =
            $this->service->update(

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

    /**
     * Elimina lógicamente un cliente.
     */
    public function destroy(
        Cliente $cliente
    ): JsonResponse {

        $this->service->destroy(
            $cliente
        );

        return response()->json([

            'message' =>
                'Cliente eliminado correctamente.'

        ], 200);
    }
}