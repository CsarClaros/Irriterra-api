<?php

namespace App\Http\Requests\Seguridad;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateRolPermisoRequest extends BaseRequest
{
    /**
     * Reglas para actualizar una relación.
     */
    public function rules(): array
    {
        $idRolPermiso = $this->route('rol_permiso')->id_rol_permiso;

        return [

            'id_rol' => [

                'required',

                'exists:rol,id_rol'

            ],

            'id_permiso' => [

                'required',

                'exists:permiso,id_permiso',

                Rule::unique('rol_permiso')
                    ->ignore(
                        $idRolPermiso,
                        'id_rol_permiso'
                    )
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