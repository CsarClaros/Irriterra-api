<?php

namespace App\Http\Requests\Inventario;

use App\Http\Requests\BaseRequest;


class BuscarProductoVarianteRequest extends BaseRequest
{
    /*
    |--------------------------------------------------------------------------
    | Reglas
    |--------------------------------------------------------------------------
    */

    public function rules(): array
    {
        return [

            'buscar' => [
                'required',
                'string',
                'min:2',
                'max:100'
            ]

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Mensajes
    |--------------------------------------------------------------------------
    */

    public function messages(): array
    {
        return [

            'buscar.required' =>
                'Ingrese un término de búsqueda.',

            'buscar.min' =>
                'Ingrese al menos 2 caracteres para buscar.',

            'buscar.max' =>
                'La búsqueda no puede superar los 100 caracteres.'

        ];
    }
}
