<?php

namespace App\Http\Requests\Seguridad;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateRolRequest extends BaseRequest
{
    /**
     * Reglas para actualizar un rol.
     */
    public function rules(): array
    {
        $idRol = $this->route('rol') ?? $this->route('id');

        return [

            'nombre' => [

                'required',

                'string',

                'max:100',

                Rule::unique('rol', 'nombre')
                    ->ignore($idRol, 'id_rol')

            ],

            'descripcion' => [
                'nullable',
                'string',
                'max:255'
            ]

        ];
    }
}