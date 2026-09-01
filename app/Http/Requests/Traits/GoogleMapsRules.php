<?php

namespace App\Http\Requests\Traits;

use Closure;


trait GoogleMapsRules
{

    /*
    |--------------------------------------------------------------------------
    | Normalizar Google Maps Embed
    |--------------------------------------------------------------------------
    */

    protected function normalizarMapsEmbed(
        mixed $valor
    ): ?string
    {

        if (
            !is_string(
                $valor
            )
        ) {

            return null;

        }


        $valor =
            trim(
                $valor
            );


        if (
            $valor === ''
        ) {

            return null;

        }


        /*
        |--------------------------------------------------------------------------
        | Detectar iframe
        |--------------------------------------------------------------------------
        */

        if (
            stripos(
                $valor,
                '<iframe'
            )
            !== false
        ) {

            $resultado =
                preg_match(

                    '/\bsrc\s*=\s*(["\'])(.*?)\1/is',

                    $valor,

                    $coincidencias

                );


            if (
                $resultado
                &&
                isset(
                    $coincidencias[2]
                )
            ) {

                $valor =
                    $coincidencias[2];

            }

        }


        /*
         * Convierte entidades como:
         *
         * &amp;
         *
         * en:
         *
         * &
         */

        return html_entity_decode(

            trim(
                $valor
            ),

            ENT_QUOTES
            |
            ENT_HTML5,

            'UTF-8'

        );

    }


    /*
    |--------------------------------------------------------------------------
    | Reglas Google Maps Embed
    |--------------------------------------------------------------------------
    */

    protected function reglasMapsEmbed():
    array
    {

        return [

            'nullable',

            'string',

            'url',

            'max:5000',

            function (
                string  $attribute,
                mixed   $value,
                Closure $fail
            ): void {

                if (
                    $value === null
                    ||
                    $value === ''
                ) {

                    return;

                }


                $host =
                    strtolower(

                        parse_url(
                            $value,
                            PHP_URL_HOST
                        )
                        ?? ''

                    );


                $path =
                    strtolower(

                        parse_url(
                            $value,
                            PHP_URL_PATH
                        )
                        ?? ''

                    );


                /*
                |--------------------------------------------------------------------------
                | Dominios permitidos
                |--------------------------------------------------------------------------
                */

                $googleCom =
                    $host
                    === 'google.com'
                    ||
                    $host
                    === 'www.google.com'
                    ||
                    $host
                    === 'maps.google.com';


                if (
                    !$googleCom
                ) {

                    $fail(
                        'La URL del mapa debe pertenecer a Google Maps.'
                    );

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | Ruta permitida
                |--------------------------------------------------------------------------
                */

                $esEmbed =
                    str_starts_with(
                        $path,
                        '/maps/embed'
                    );


                $esMaps =
                    $host
                    === 'maps.google.com'
                    &&
                    str_starts_with(
                        $path,
                        '/maps'
                    );


                if (
                    !$esEmbed
                    &&
                    !$esMaps
                ) {

                    $fail(
                        'La URL ingresada no corresponde a un mapa incrustado válido.'
                    );

                }

            }

        ];

    }

}
