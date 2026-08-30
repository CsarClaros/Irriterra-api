<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reportes\ReporteKardexInventarioRequest;
use App\Http\Requests\Reportes\ReporteStockInventarioRequest;
use App\Http\Resources\Reportes\ReporteMovimientoInventarioResource;
use App\Http\Resources\Reportes\ReporteStockInventarioResource;
use App\Services\Reportes\ReporteInventarioService;
use Illuminate\Http\JsonResponse;

class ReporteInventarioController extends Controller
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly ReporteInventarioService $service
    ) {
    }

    /**
     * Reporte general de stock.
     */
    public function stock(
        ReporteStockInventarioRequest $request
    ): JsonResponse {

        $resultado =
            $this->service->stock(

                $request->validated()

            );

        return response()->json([

            'resumen' =>
                $resultado['resumen'],

            'data' =>
                ReporteStockInventarioResource::collection(

                    $resultado['data']

                )

        ]);
    }

    /**
     * Reporte de stock bajo o agotado.
     */
    public function stockBajoMinimo(
        ReporteStockInventarioRequest $request
    ): JsonResponse {

        $resultado =
            $this->service
                ->stockBajoMinimo(

                    $request->validated()

                );

        return response()->json([

            'resumen' =>
                $resultado['resumen'],

            'data' =>
                ReporteStockInventarioResource::collection(

                    $resultado['data']

                )

        ]);
    }

    /**
     * Reporte de valoración del inventario.
     */
    public function valoracion(
        ReporteStockInventarioRequest $request
    ): JsonResponse {

        $resultado =
            $this->service->valoracion(

                $request->validated()

            );

        return response()->json([

            'resumen' =>
                $resultado['resumen'],

            'data' =>
                ReporteStockInventarioResource::collection(

                    $resultado['data']

                )

        ]);
    }

    /**
     * Kardex de inventario.
     */
    public function kardex(
        ReporteKardexInventarioRequest $request
    ): JsonResponse {

        $resultado =
            $this->service->kardex(

                $request->validated()

            );

        return response()->json([

            'resumen' =>
                $resultado['resumen'],

            'data' =>
                ReporteMovimientoInventarioResource::collection(

                    $resultado['data']

                )

        ]);
    }
}