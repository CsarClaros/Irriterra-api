<?php

namespace App\Http\Requests\Traits;

trait ObservacionRules
{
    /**
     * Reglas relacionadas con observaciones.
     */
    protected function observacionRules(): array
    {
        return [

            'observaciones' => [
                'nullable',
                'string',
                'max:500'
            ]

        ];
    }
}