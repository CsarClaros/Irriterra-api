<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reportes\ReporteComprasRequest;
use App\Http\Resources\Compras\CompraResource;
use App\Http\Resources\Reportes\ReporteProductoOperacionResource;
use App\Services\Reportes\ReporteComprasService;
use Illuminate\Http\JsonResponse;

class ReporteComprasController extends Controller
{
    public function __construct(
        private readonly ReporteComprasService $service
    ) {
    }

    public function resumen(
        ReporteComprasRequest $request
    ): JsonResponse {

        $resultado =
            $this->service->resumen(
                $request->validated()
            );

        return response()->json([

            'resumen' =>
                $resultado['resumen'],

            'data' =>
                CompraResource::collection(
                    $resultado['data']
                )

        ]);
    }

    public function productos(
        ReporteComprasRequest $request
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