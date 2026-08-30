<?php

namespace App\Http\Requests\Organizacion;

use App\Http\Requests\BaseRequest;
use App\Http\Requests\Traits\AuditoriaRules;
use App\Http\Requests\Traits\ContactoRules;
use App\Http\Requests\Traits\EstadoRegistroRules;
use App\Http\Requests\Traits\UbicacionRules;

class StoreSucursalRequest extends BaseRequest
{
    use ContactoRules;
    use UbicacionRules;
    use EstadoRegistroRules;
    use AuditoriaRules;

    /**
     * Reglas para registrar una sucursal.
     */
    public function rules(): array
    {
        return array_merge(

            [

                'id_empresa' => [
                    'required',
                    'exists:empresa,id_empresa'
                ],

                'codigo' => [
                    'required',
                    'string',
                    'max:20',
                    'unique:sucursal,codigo'
                ],

                'nombre' => [
                    'required',
                    'string',
                    'max:150'
                ]

            ],

            $this->ubicacionRules(),

            $this->contactoRules(),

            // $this->auditoriaRules(),

            // $this->estadoRegistroRules()

        );
    }
}