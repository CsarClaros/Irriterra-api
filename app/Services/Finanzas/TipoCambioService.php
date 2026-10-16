<?php

namespace App\Services\Finanzas;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class TipoCambioService
{
    /**
     * Obtiene el tipo de cambio actual USD -> BOB.
     */
    public function obtenerActual(): array
    {
        $cacheMinutes =
            max(
                1,
                (int)config(
                    'tipo_cambio.cache_minutes',
                    60
                )
            );

        return Cache::remember(
            'tipo_cambio.usd_bob.actual',
            now()->addMinutes(
                $cacheMinutes
            ),
            function (): array {

                $response =
                    Http::acceptJson()
                        ->timeout(
                            (int)config(
                                'tipo_cambio.timeout',
                                5
                            )
                        )
                        ->retry(
                            2,
                            250
                        )
                        ->get(
                            config(
                                'tipo_cambio.url'
                            )
                        );

                if (!$response->successful()) {

                    throw new RuntimeException(
                        'No fue posible consultar el tipo de cambio.'
                    );
                }

                $datos =
                    $response->json(
                        'datos'
                    );

                if (
                    !is_array($datos)
                    ||
                    !isset(
                        $datos['usd_bob']
                    )
                    ||
                    !is_numeric(
                        $datos['usd_bob']
                    )
                    ||
                    (float)
                    $datos['usd_bob']
                    <= 0
                ) {

                    throw new RuntimeException(
                        'La fuente de tipo de cambio devolvió una respuesta inválida.'
                    );
                }

                return [

                    'moneda_origen' =>
                        'USD',

                    'moneda_destino' =>
                        'BOB',

                    'tipo_cambio' =>
                        round(
                            (float)
                            $datos['usd_bob'],
                            6
                        ),

                    'fuente' =>
                        $datos['fuente']
                        ?? 'Banco Central de Bolivia',

                    'fecha_consulta' =>
                        now()
                            ->toIso8601String()

                ];
            }
        );
    }
}
