<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Fuente de tipo de cambio USD / BOB
    |--------------------------------------------------------------------------
    */

    'url' => env(
        'TIPO_CAMBIO_URL',
        'https://api.factura.bo/ExchangeRate'
    ),

    /*
    |--------------------------------------------------------------------------
    | Timeout HTTP
    |--------------------------------------------------------------------------
    */

    'timeout' => (int)env(
        'TIPO_CAMBIO_TIMEOUT',
        10
    ),

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | La fuente se actualiza diariamente.
    | No necesitamos consultar la API externa
    | cada vez que se abre el formulario.
    |
    */

    'cache_minutes' => (int)env(
        'TIPO_CAMBIO_CACHE_MINUTES',
        60
    )

];
