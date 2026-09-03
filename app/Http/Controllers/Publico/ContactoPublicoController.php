<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Http\Requests\Publico\EnviarContactoRequest;
use App\Mail\ContactoWebMail;
use App\Services\Publico\TurnstileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Throwable;


class ContactoPublicoController
    extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct(

        private readonly TurnstileService $turnstileService

    )
    {
    }


    /*
    |--------------------------------------------------------------------------
    | Enviar contacto
    |--------------------------------------------------------------------------
    */

    public function store(
        EnviarContactoRequest $request
    ): JsonResponse
    {

        $datos =
            $request->validated();


        /*
        |--------------------------------------------------------------------------
        | Turnstile
        |--------------------------------------------------------------------------
        */

        $token =
            $datos['turnstile_token'];


        if (
            !$this
                ->turnstileService
                ->verificar(
                    $token
                )
        ) {

            return response()->json(
                [

                    'message' =>
                        'No fue posible validar la verificación de seguridad.'

                ],
                422
            );

        }


        /*
        |--------------------------------------------------------------------------
        | El token no forma parte del correo
        |--------------------------------------------------------------------------
        */

        unset(
            $datos['turnstile_token']
        );


        /*
        |--------------------------------------------------------------------------
        | Enviar correo
        |--------------------------------------------------------------------------
        */

        try {

            Mail::to(
                config(
                    'contacto.destino'
                )
            )
                ->send(
                    new ContactoWebMail(
                        $datos
                    )
                );


            return response()->json([

                'message' =>
                    'Mensaje enviado correctamente.'

            ]);

        } catch (
        Throwable $exception
        ) {

            report(
                $exception
            );


            return response()->json(
                [

                    'message' =>
                        'No fue posible enviar el mensaje en este momento.'

                ],
                503
            );

        }

    }

}
