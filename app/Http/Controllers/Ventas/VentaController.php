<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ventas\StoreVentaRequest;
use App\Http\Requests\Ventas\UpdateVentaRequest;
use App\Http\Resources\Ventas\VentaCollection;
use App\Http\Resources\Ventas\VentaResource;
use App\Models\Ventas\Venta;
use App\Services\Ventas\VentaService;
use Illuminate\Http\JsonResponse;

use App\Http\Requests\Ventas\AnularVentaRequest;
use App\Http\Requests\Ventas\CompletarVentaRequest;

class VentaController extends Controller
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly VentaService $service
    ) {}

    /**
     * Lista ventas activas.
     */
    public function index(): VentaCollection
    {
        return new VentaCollection(

            $this->service->index()

        );
    }

    /**
     * Registra una venta en borrador.
     */
    public function store(
        StoreVentaRequest $request
    ): JsonResponse {

        $venta =
            $this->service->store(

                $request->validated()

            );

        return response()->json(

            new VentaResource(
                $venta
            ),

            201

        );
    }

    /**
     * Muestra una venta.
     */
    public function show(
        Venta $venta
    ): VentaResource {

        return new VentaResource(

            $this->service->show(

                $venta->id_venta

            )

        );
    }

    /**
     * Actualiza una venta en borrador.
     */
    public function update(
        UpdateVentaRequest $request,
        Venta $venta
    ): JsonResponse {

        $venta =
            $this->service->update(

                $venta,

                $request->validated()

            );

        return response()->json(

            new VentaResource(
                $venta
            ),

            200

        );
    }

    /**
     * Completa la venta y registra el pago.
     */
    public function completar(
        CompletarVentaRequest $request,
        Venta $venta
    ): JsonResponse {

        $venta =
            $this->service->completar(

                $venta,

                $request->validated()

            );

        return response()->json(

            new VentaResource(
                $venta
            ),

            200

        );
    }

    /**
     * Anula una venta completada.
     */
    public function anular(
        AnularVentaRequest $request,
        Venta $venta
    ): JsonResponse {

        $venta =
            $this->service->anular(

                $venta,

                $request->validated()

            );

        return response()->json(

            new VentaResource(
                $venta
            ),

            200

        );
    }

    /**
     * Elimina lógicamente una venta.
     */
    public function destroy(
        Venta $venta
    ): JsonResponse {

        $this->service->destroy(
            $venta
        );

        return response()->json([

            'message' =>
            'Venta eliminada correctamente.'

        ], 200);
    }
}
