<?php

namespace App\Http\Requests\Seguridad;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdatePermisoRequest extends BaseRequest
{
    /**
     * Reglas para actualizar un permiso.
     */
    public function rules(): array
    {
        $idPermiso = $this->route('permiso') ?? $this->route('id');

        return [

            'nombre' => [

                'required',

                'string',

                'max:150',

                Rule::unique('permiso', 'nombre')
                    ->ignore($idPermiso, 'id_permiso')

            ],

            'descripcion' => [

                'nullable',

                'string',

                'max:255'

            ]

        ];
    }
}