<?php

namespace App\Http\Requests\Ventas;

use App\Http\Requests\BaseRequest;
use App\Models\Ventas\Cliente;
use Illuminate\Validation\Rule;

class UpdateClienteRequest extends BaseRequest
{
    /**
     * Reglas para actualizar un cliente.
     */
    public function rules(): array
    {
        $cliente =
            $this->route('cliente');

        $idCliente =
            $cliente instanceof Cliente
                ? $cliente->id_cliente
                : $cliente;

        return [

            'tipo_cliente' => [

                'sometimes',

                'required',

                'string',

                Rule::in(
                    Cliente::TIPOS
                )

            ],

            'nombre_razon_social' => [

                'sometimes',

                'required',

                'string',

                'max:150'

            ],

            'tipo_documento' => [

                'sometimes',

                'nullable',

                'string',

                'max:20'

            ],

            'numero_documento' => [

                'sometimes',

                'nullable',

                'string',

                'max:30',

                Rule::unique(
                    'cliente',
                    'numero_documento'
                )
                    ->ignore(
                        $idCliente,
                        'id_cliente'
                    )

            ],

            'telefono' => [

                'sometimes',

                'nullable',

                'string',

                'max:30'

            ],

            'correo' => [

                'sometimes',

                'nullable',

                'email',

                'max:150'

            ],

            'direccion' => [

                'sometimes',

                'nullable',

                'string',

                'max:255'

            ],

            'observaciones' => [

                'sometimes',

                'nullable',

                'string'

            ],

            'usuario_modificacion' => [

                'sometimes',

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

            'usuario_creacion' => [

                'prohibited'

            ]

        ];
    }
}