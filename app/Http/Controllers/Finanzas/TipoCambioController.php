<?php

namespace App\Http\Controllers\Finanzas;

use App\Http\Controllers\Controller;
use App\Services\Finanzas\TipoCambioService;
use Illuminate\Http\JsonResponse;
use Throwable;

class TipoCambioController extends Controller
{
    public function actual(
        TipoCambioService $service
    ): JsonResponse
    {

        try {

            return response()->json([

                'data' =>
                    $service->obtenerActual()

            ]);

        } catch (Throwable $exception) {

            report(
                $exception
            );

            return response()->json(
                [

                    'message' =>
                        'No fue posible obtener el tipo de cambio automáticamente.',

                    'data' =>
                        null

                ],
                503
            );
        }
    }
}
