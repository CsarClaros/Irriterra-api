<?php

namespace App\Http\Requests\Seguridad;

use App\Http\Requests\BaseRequest;

class LoginRequest extends BaseRequest
{
    /**
     * Reglas para iniciar sesión.
     */
    public function rules(): array
    {
        return [

            'usuario' => [

                'required',

                'string',

                'max:50'

            ],

            'contrasena' => [

                'required',

                'string',

                'max:255'

            ]

        ];
    }

    /**
     * Normaliza el nombre de usuario.
     */
    protected function prepareForValidation(): void
    {
        if (
            $this->filled('usuario')
        ) {

            $this->merge([

                'usuario' =>
                    strtolower(
                        trim(
                            $this->input(
                                'usuario'
                            )
                        )
                    )

            ]);

        }
    }
}