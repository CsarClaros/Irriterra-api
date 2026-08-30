<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reportes\ReporteTransferenciasRequest;
use App\Http\Resources\Reportes\ReporteProductoOperacionResource;
use App\Http\Resources\Transferencias\TransferenciaInventarioResource;
use App\Services\Reportes\ReporteTransferenciasService;
use Illuminate\Http\JsonResponse;

class ReporteTransferenciasController extends Controller
{
    public function __construct(
        private readonly ReporteTransferenciasService $service
    ) {
    }

    public function resumen(
        ReporteTransferenciasRequest $request
    ): JsonResponse {

        $resultado =
            $this->service->resumen(
                $request->validated()
            );

        return response()->json([

            'resumen' =>
                $resultado['resumen'],

            'data' =>
                TransferenciaInventarioResource::collection(
                    $resultado['data']
                )

        ]);
    }

    public function productos(
        ReporteTransferenciasRequest $request
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