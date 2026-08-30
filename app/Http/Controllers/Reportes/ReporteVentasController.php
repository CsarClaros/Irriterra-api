<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reportes\ReporteVentasRequest;
use App\Http\Resources\Reportes\ReporteProductoOperacionResource;
use App\Http\Resources\Ventas\VentaResource;
use App\Services\Reportes\ReporteVentasService;
use Illuminate\Http\JsonResponse;

class ReporteVentasController extends Controller
{
    public function __construct(
        private readonly ReporteVentasService $service
    ) {
    }

    public function resumen(
        ReporteVentasRequest $request
    ): JsonResponse {

        $resultado =
            $this->service->resumen(
                $request->validated()
            );

        return response()->json([

            'resumen' =>
                $resultado['resumen'],

            'data' =>
                VentaResource::collection(
                    $resultado['data']
                )

        ]);
    }

    public function productos(
        ReporteVentasRequest $request
    ): JsonResponse {

        $resultado =
            $this->service->productos(
                $request->validated()
            );

        return response()->json([

            'resumen' =>
                $resultado['resumen'],

            'data' =>
                ReporteProductoOperacionResource::collection(
                    $resultado['data']
                )

        ]);
    }
}