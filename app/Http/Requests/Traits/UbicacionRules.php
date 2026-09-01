<?php

namespace App\Http\Requests\Traits;

trait UbicacionRules
{
    /**
     * Reglas relacionadas con ubicación.
     */
    protected function ubicacionRules(): array
    {
        return [

            'departamento' => [
                'required',
                'string',
                'max:100'
            ],

            'ciudad' => [
                'required',
                'string',
                'max:100'
            ],

            'direccion' => [
                'nullable',
                'string',
                'max:255'
            ],


        ];
    }
}
