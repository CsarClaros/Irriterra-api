<?php

namespace App\Http\Requests\Seguridad;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class ActualizarPerfilRequest extends BaseRequest
{
    /**
     * Reglas para actualizar los datos
     * personales del usuario autenticado.
     */
    public function rules(): array
    {
        return [

            'correo' => [

                'nullable',

                'email',

                'max:150',

                Rule::unique(
                    'usuario',
                    'correo'
                )
                    ->ignore(
                        $this->user()
                            ?->id_usuario,

                        'id_usuario'
                    )

            ],


            'telefono' => [

                'nullable',

                'string',

                'max:20'

            ],


            'direccion' => [

                'nullable',

                'string',

                'max:255'

            ]

        ];
    }
}
