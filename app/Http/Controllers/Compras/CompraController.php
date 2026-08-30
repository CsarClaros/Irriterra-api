<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Http\Requests\Compras\StoreCompraRequest;
use App\Http\Requests\Compras\UpdateCompraRequest;
use App\Http\Resources\Compras\CompraCollection;
use App\Http\Resources\Compras\CompraResource;
use App\Models\Compras\Compra;
use App\Services\Compras\CompraService;
use Illuminate\Http\JsonResponse;

use App\Http\Requests\Compras\AnularCompraRequest;
use App\Http\Requests\Compras\ConfirmarCompraRequest;
use App\Http\Requests\Compras\RecibirCompraRequest;

class CompraController extends Controller
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly CompraService $service
    ) {}

    /**
     * Lista compras activas.
     */
    public function index(): CompraCollection
    {
        return new CompraCollection(

            $this->service->index()

        );
    }

    /**
     * Registra una compra en borrador.
     */
    public function store(
        StoreCompraRequest $request
    ): JsonResponse {

        $compra =
            $this->service->store(

                $request->validated()

            );

        return response()->json(

            new CompraResource(
                $compra
            ),

            201

        );
    }

    /**
     * Muestra una compra activa.
     */
    public function show(
        Compra $compra
    ): CompraResource {

        return new CompraResource(

            $this->service->show(

                $compra->id_compra

            )

        );
    }

    /**
     * Actualiza una compra en borrador.
     */
    public function update(
        UpdateCompraRequest $request,
        Compra $compra
    ): JsonResponse {

        $compra =
            $this->service->update(

                $compra,

                $request->validated()

            );

        return response()->json(

            new CompraResource(
                $compra
            ),

            200

        );
    }


    /**
     * Confirma una compra.
     */
    public function confirmar(
        ConfirmarCompraRequest $request,
        Compra $compra
    ): JsonResponse {

        $compra =
            $this->service->confirmar(

                $compra,

                $request->validated()

            );

        return response()->json(

            new CompraResource(
                $compra
            ),

            200

        );
    }

    /**
     * Recibe una compra confirmada.
     */
    public function recibir(
        RecibirCompraRequest $request,
        Compra $compra
    ): JsonResponse {

        $compra =
            $this->service->recibir(

                $compra,

                $request->validated()

            );

        return response()->json(

            new CompraResource(
                $compra
            ),

            200

        );
    }

    /**
     * Anula una compra no recibida.
     */
    public function anular(
        AnularCompraRequest $request,
        Compra $compra
    ): JsonResponse {

        $compra =
            $this->service->anular(

                $compra,

                $request->validated()

            );

        return response()->json(

            new CompraResource(
                $compra
            ),

            200

        );
    }

    /**
     * Elimina lógicamente una compra.
     */
    public function destroy(
        Compra $compra
    ): JsonResponse {

        $this->service->destroy(
            $compra
        );

        return response()->json([

            'message' =>
            'Compra eliminada correctamente.'

        ], 200);
    }
}
