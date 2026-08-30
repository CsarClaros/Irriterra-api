<?php

namespace App\Http\Requests\Compras;

use App\Http\Requests\BaseRequest;
use App\Models\Compras\Proveedor;
use Illuminate\Validation\Rule;

class UpdateProveedorRequest extends BaseRequest
{
    /**
     * Prepara los datos antes de validarlos.
     */
    protected function prepareForValidation(): void
    {
        $data = [];

        if (
            $this->exists(
                'numero_documento'
            )
        ) {

            $data['numero_documento'] =
                $this->numero_documento === ''
                    ? null
                    : $this->numero_documento;

        }

        if (
            $this->exists(
                'correo'
            )
        ) {

            $data['correo'] =
                $this->correo === ''
                    ? null
                    : $this->correo;

        }

        if (
            $this->exists(
                'telefono'
            )
        ) {

            $data['telefono'] =
                $this->telefono === ''
                    ? null
                    : $this->telefono;

        }

        if (! empty($data)) {

            $this->merge($data);

        }
    }

    /**
     * Reglas para actualizar un proveedor.
     */
    public function rules(): array
    {
        $proveedor =
            $this->route(
                'proveedor'
            );

        $idProveedor =
            $proveedor instanceof Proveedor

                ? $proveedor->id_proveedor

                : $proveedor;

        return [

            'tipo_proveedor' => [

                'sometimes',

                'required',

                'string',

                Rule::in(
                    Proveedor::TIPOS
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
                    'proveedor',
                    'numero_documento'
                )
                    ->ignore(
                        $idProveedor,
                        'id_proveedor'
                    )

            ],

            'nombre_contacto' => [

                'sometimes',

                'nullable',

                'string',

                'max:150'

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

            'ciudad' => [

                'sometimes',

                'nullable',

                'string',

                'max:100'

            ],

            'departamento' => [

                'sometimes',

                'nullable',

                'string',

                'max:100'

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