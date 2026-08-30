<?php

namespace App\Http\Requests\Traits;

trait EstadoRegistroRules
{
    /**
     * Estado del registro.
     */
    protected function estadoRegistroRules(): array
    {
        return [

            'estado_registro' => [
                'required',
                'in:A,I'
            ]

        ];
    }
}