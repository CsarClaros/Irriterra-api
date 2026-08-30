<?php

namespace App\Http\Requests\Seguridad;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioRequest extends BaseRequest
{
    /**
     * Reglas para actualizar un usuario.
     */
    public function rules(): array
    {
        $idUsuario = $this->route('usuario')->id_usuario;

        return [

            'id_rol' => [

                'required',

                'exists:rol,id_rol'

            ],

            'id_sucursal' => [

                'required',

                'exists:sucursal,id_sucursal'

            ],

            'ci' => [

                'required',

                'string',

                'max:20',

                Rule::unique('usuario','ci')

                    ->ignore(

                        $idUsuario,

                        'id_usuario'

                    )

            ],

            /*
            'usuario' => [

                'required',

                'string',

                'max:50',

                Rule::unique('usuario','usuario')

                    ->ignore(

                        $idUsuario,

                        'id_usuario'

                    )

            ],

            'password' => [

                'nullable',

                'string',

                'min:8',

                'max:255'

            ],
            */

            'nombre' => [

                'required',

                'string',

                'max:100'

            ],

            'apellido_paterno' => [

                'required',

                'string',

                'max:100'

            ],

            'apellido_materno' => [

                'nullable',

                'string',

                'max:100'

            ],

            'correo' => [

                'nullable',

                'email',

                'max:150',

                Rule::unique('usuario','correo')

                    ->ignore(

                        $idUsuario,

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

            ],

            'foto' => [

                'nullable',

                'image',

                'mimes:jpg,jpeg,png,webp',

                'max:2048'

            ]

        ];
    }
}
