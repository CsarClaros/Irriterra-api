<?php

namespace App\Http\Requests\Traits;

trait ContactoRules
{
    /**
     * Reglas comunes de contacto.
     */
    protected function contactoRules(): array
    {
        return [

            'telefono' => [
                'nullable',
                'string',
                'max:20'
            ],

            'correo' => [
                'nullable',
                'email',
                'max:150'
            ],

            'sitio_web' => [
                'nullable',
                'url',
                'max:255'
            ]

        ];
    }
}