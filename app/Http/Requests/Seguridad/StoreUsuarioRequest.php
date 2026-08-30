<?php

namespace App\Http\Requests\Seguridad;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class StoreUsuarioRequest extends BaseRequest
{
    /**
     * Reglas para registrar un usuario.
     */
    public function rules(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Relaciones
            |--------------------------------------------------------------------------
            */

            'id_rol' => [

                'required',

                'exists:rol,id_rol'

            ],

            'id_sucursal' => [

                'required',

                'exists:sucursal,id_sucursal'

            ],

            /*
            |--------------------------------------------------------------------------
            | Acceso
            |--------------------------------------------------------------------------
            */

            'ci' => [

                'required',

                'string',

                'max:20',

                'unique:usuario,ci'

            ],

            // 'usuario' => [

            //     'required',

            //     'string',

            //     'max:50',

            //     'unique:usuario,usuario'

            // ],

            // 'password' => [

            //     'required',

            //     'string',

            //     'min:8',

            //     'max:255'

            // ],

            /*
            |--------------------------------------------------------------------------
            | Datos personales
            |--------------------------------------------------------------------------
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

                'unique:usuario,correo'

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
