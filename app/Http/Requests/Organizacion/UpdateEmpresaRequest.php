<?php

namespace App\Http\Requests\Organizacion;

use App\Http\Requests\BaseRequest;
use App\Http\Requests\Traits\AuditoriaRules;
use App\Http\Requests\Traits\ContactoRules;
use App\Http\Requests\Traits\EstadoRegistroRules;
use App\Http\Requests\Traits\ObservacionRules;
use Illuminate\Validation\Rule;

class UpdateEmpresaRequest extends BaseRequest
{
    use ContactoRules;
    use EstadoRegistroRules;
    use AuditoriaRules;
    use ObservacionRules;

    /**
     * Reglas para actualizar una empresa.
     */
    public function rules(): array
    {
        $idEmpresa = $this->route('empresa') ?? $this->route('id');

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

                    Rule::unique('empresa', 'nit')
                        ->ignore($idEmpresa, 'id_empresa')

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

            $this->observacionRules(),

            // $this->auditoriaRules(),

            // $this->estadoRegistroRules()

        );
    }
}