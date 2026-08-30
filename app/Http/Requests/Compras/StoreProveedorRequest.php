<?php

namespace App\Http\Requests\Compras;

use App\Http\Requests\BaseRequest;
use App\Models\Compras\Proveedor;
use Illuminate\Validation\Rule;

class StoreProveedorRequest extends BaseRequest
{
    /**
     * Prepara los datos antes de validarlos.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([

            'numero_documento' =>
                $this->numero_documento === ''
                    ? null
                    : $this->numero_documento,

            'correo' =>
                $this->correo === ''
                    ? null
                    : $this->correo,

            'telefono' =>
                $this->telefono === ''
                    ? null
                    : $this->telefono

        ]);
    }

    /**
     * Reglas para registrar un proveedor.
     */
    public function rules(): array
    {
        return [

            'tipo_proveedor' => [

                'required',

                'string',

                Rule::in(
                    Proveedor::TIPOS
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
                    'proveedor',
                    'numero_documento'
                )

            ],

            'nombre_contacto' => [

                'nullable',

                'string',

                'max:150'

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

            'ciudad' => [

                'nullable',

                'string',

                'max:100'

            ],

            'departamento' => [

                'nullable',

                'string',

                'max:100'

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