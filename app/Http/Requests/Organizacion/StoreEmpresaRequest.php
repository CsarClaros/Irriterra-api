<?php

namespace App\Http\Requests\Organizacion;

use App\Http\Requests\BaseRequest;
use App\Http\Requests\Traits\AuditoriaRules;
use App\Http\Requests\Traits\ContactoRules;
use App\Http\Requests\Traits\EstadoRegistroRules;

class StoreEmpresaRequest extends BaseRequest
{
    use ContactoRules;
    use EstadoRegistroRules;
    use AuditoriaRules;

    /**
     * Reglas de validación para registrar una empresa.
     */
    public function rules(): array
    {
        return array_merge(

            [

                'nombre' => [
                    'required',
                    'string',
                    'max:150'
                ],

                'nit' => [
                    'required',
                    'string',
                    'max:30',
                    'unique:empresa,nit'
                ],

                'direccion' => [
                    'nullable',
                    'string',
                    'max:255'
                ],

                'logo' => [
                    'nullable',
                    'string',
                    'max:255'
                ]

            ],

            $this->contactoRules(),

            // $this->auditoriaRules(),

            // $this->estadoRegistroRules()

        );
    }
}