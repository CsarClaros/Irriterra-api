<?php

namespace App\Services\Publico;

use Illuminate\Support\Facades\Http;


class TurnstileService
{

    /*
    |--------------------------------------------------------------------------
    | Verificar token
    |--------------------------------------------------------------------------
    */

    public function verificar(
        string $token
    ): bool
    {

        $secret =
            config(
                'turnstile.secret_key'
            );


        if (
            !is_string($secret)
            ||
            $secret === ''
        ) {

            return false;

        }


        $response =
            Http::asForm()
                ->timeout(
                    10
                )
                ->post(
                    'https://challenges.cloudflare.com/turnstile/v0/siteverify',
                    [
                        'secret' =>
                            $secret,

                        'response' =>
                            $token
                    ]
                );


        if (
            !$response->successful()
        ) {

            return false;

        }


        $datos =
            $response->json();


        /*
        |--------------------------------------------------------------------------
        | Resultado
        |--------------------------------------------------------------------------
        */

        if (
            !($datos['success'] ?? false)
        ) {

            return false;

        }

        /*
 |--------------------------------------------------------------------------
 | Claves oficiales de prueba
 |--------------------------------------------------------------------------
 */

        $esClavePrueba =
            (
                $datos[
                'metadata'
                ][
                'result_with_testing_key'
                ]
                ?? false
            )
            === true;


        if (
            $esClavePrueba
        ) {

            /*
             * Las claves de prueba jamás deben
             * aceptarse fuera del entorno local.
             */

            if (
                !app()
                    ->environment(
                        'local'
                    )
            ) {

                return false;

            }


            /*
             * La Site Key TEST utilizada por Angular
             * genera este token dummy conocido.
             *
             * La Secret Key TEST de Cloudflare está
             * diseñada para aprobar validaciones,
             * por lo que en desarrollo comprobamos
             * explícitamente el token esperado.
             */

            return hash_equals(
                'XXXX.DUMMY.TOKEN.XXXX',
                $token
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Acción
        |--------------------------------------------------------------------------
        */

        if (
            ($datos['action'] ?? null)
            !==
            config(
                'turnstile.action'
            )
        ) {

            return false;

        }


        /*
        |--------------------------------------------------------------------------
        | Hostname
        |--------------------------------------------------------------------------
        */

        $hostname =
            $datos['hostname']
            ?? null;


        $hostnamesPermitidos =
            config(
                'turnstile.hostnames',
                []
            );


        if (
            !is_string(
                $hostname
            )
            ||
            !in_array(
                $hostname,
                $hostnamesPermitidos,
                true
            )
        ) {

            return false;

        }


        return true;

    }

}
