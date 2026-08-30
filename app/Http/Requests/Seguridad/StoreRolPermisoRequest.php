<?php

namespace App\Http\Requests\Seguridad;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class StoreRolPermisoRequest extends BaseRequest
{
    /**
     * Reglas para registrar una relación Rol-Permiso.
     */
    public function rules(): array
    {
        return [

            'id_rol' => [

                'required',

                'exists:rol,id_rol'

            ],

            'id_permiso' => [

                'required',

                'exists:permiso,id_permiso',

                Rule::unique('rol_permiso')
                    ->where(function ($query) {

                        return $query->where(
                            'id_rol',
                            $this->id_rol
                        );

                    })

            ]

        ];
    }

    /**
     * Mensajes personalizados.
     */
    public function messages(): array
    {
        return [

            'id_permiso.unique' =>
                'El permiso ya fue asignado al rol seleccionado.'

        ];
    }
}