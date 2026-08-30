<?php

namespace App\Http\Controllers\Transferencias;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transferencias\CompletarTransferenciaInventarioRequest;
use App\Http\Requests\Transferencias\EnviarTransferenciaInventarioRequest;
use App\Http\Requests\Transferencias\RechazarTransferenciaInventarioRequest;
use App\Http\Requests\Transferencias\StoreTransferenciaInventarioRequest;
use App\Http\Requests\Transferencias\UpdateTransferenciaInventarioRequest;
use App\Http\Resources\Transferencias\TransferenciaInventarioCollection;
use App\Http\Resources\Transferencias\TransferenciaInventarioResource;
use App\Models\Transferencias\TransferenciaInventario;
use App\Services\Transferencias\TransferenciaInventarioService;
use Illuminate\Http\JsonResponse;

class TransferenciaInventarioController extends Controller
{
    public function __construct(
        private readonly TransferenciaInventarioService $service
    ) {
    }

    public function index(): TransferenciaInventarioCollection
    {
        return new TransferenciaInventarioCollection(

            $this->service->index()

        );
    }

    public function store(
        StoreTransferenciaInventarioRequest $request
    ): JsonResponse {

        $transferenciaInventario =
            $this->service->store(

                $request->validated()

            );

        return response()->json(

            new TransferenciaInventarioResource(

                $transferenciaInventario

            ),

            201

        );
    }

    public function show(
        TransferenciaInventario $transferenciaInventario
    ): TransferenciaInventarioResource {

        return new TransferenciaInventarioResource(

            $this->service->show(

                $transferenciaInventario
                    ->id_transferencia_inventario

            )

        );
    }

    public function update(
        UpdateTransferenciaInventarioRequest $request,
        TransferenciaInventario $transferenciaInventario
    ): JsonResponse {

        $transferenciaInventario =
            $this->service->update(

                $transferenciaInventario,

                $request->validated()

            );

        return response()->json(

            new TransferenciaInventarioResource(

                $transferenciaInventario

            ),

            200

        );
    }

    public function enviar(
        EnviarTransferenciaInventarioRequest $request,
        TransferenciaInventario $transferenciaInventario
    ): JsonResponse {

        $transferenciaInventario =
            $this->service->enviar(

                $transferenciaInventario,

                $request->validated()

            );

        return response()->json(

            new TransferenciaInventarioResource(

                $transferenciaInventario

            ),

            200

        );
    }

    public function completar(
        CompletarTransferenciaInventarioRequest $request,
        TransferenciaInventario $transferenciaInventario
    ): JsonResponse {

        $transferenciaInventario =
            $this->service->completar(

                $transferenciaInventario,

                $request->validated()

            );

        return response()->json(

            new TransferenciaInventarioResource(

                $transferenciaInventario

            ),

            200

        );
    }

    public function rechazar(
        RechazarTransferenciaInventarioRequest $request,
        TransferenciaInventario $transferenciaInventario
    ): JsonResponse {

        $transferenciaInventario =
            $this->service->rechazar(

                $transferenciaInventario,

                $request->validated()

            );

        return response()->json(

            new TransferenciaInventarioResource(

                $transferenciaInventario

            ),

            200

        );
    }

    public function destroy(
        TransferenciaInventario $transferenciaInventario
    ): JsonResponse {

        $this->service->destroy(
            $transferenciaInventario
        );

        return response()->json([

            'message' =>
                'Transferencia de inventario eliminada correctamente.'

        ], 200);
    }
}