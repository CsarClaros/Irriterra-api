<?php

namespace App\Http\Requests\Ventas;

use App\Http\Requests\BaseRequest;
use App\Models\Ventas\Cliente;
use Illuminate\Validation\Rule;

class StoreClienteRequest extends BaseRequest
{
    /**
     * Reglas para registrar un cliente.
     */
    public function rules(): array
    {
        return [

            'tipo_cliente' => [

                'required',

                'string',

                Rule::in(
                    Cliente::TIPOS
                )

            ],

            'nombre_razon_social' => [

                'required',

                'string',

                'max:150'

            ],

            'tipo_documento' => [

                'nullable',

                'string',

                'max:20'

            ],

            'numero_documento' => [

                'nullable',

                'string',

                'max:30',

                Rule::unique(
                    'cliente',
                    'numero_documento'
                )

            ],

            'telefono' => [

                'nullable',

                'string',

                'max:30'

            ],

            'correo' => [

                'nullable',

                'email',

                'max:150'

            ],

            'direccion' => [

                'nullable',

                'string',

                'max:255'

            ],

            'observaciones' => [

                'nullable',

                'string'

            ],

            'usuario_creacion' => [

                'nullable',

                'integer',

                Rule::exists(
                    'usuario',
                    'id_usuario'
                )
                    ->where(
                        'estado_registro',
                        'A'
                    )

            ],

            'estado_registro' => [

                'prohibited'

            ],

            'usuario_modificacion' => [

                'prohibited'

            ]

        ];
    }
}