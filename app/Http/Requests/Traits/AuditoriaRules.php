<?php

namespace App\Http\Requests\Traits;

trait AuditoriaRules
{
    /**
     * Observaciones generales.
     */
    protected function auditoriaRules(): array
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