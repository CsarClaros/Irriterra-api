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

        $empresa =
            $this->route(
                'empresa'
            );


        $idEmpresa =
            $empresa
                ?->id_empresa
            ??
            $empresa
            ??
            $this->route(
                'id'
            );


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

                    Rule::unique(
                        'empresa',
                        'nit'
                    )
                        ->ignore(
                            $idEmpresa,
                            'id_empresa'
                        )

                ],


                'direccion' => [
                    'nullable',
                    'string',
                    'max:255'
                ],


                'sitio_web' => [
                    'nullable',
                    'url',
                    'max:255'
                ],


                'logo' => [
                    'sometimes',
                    'nullable',
                    'file',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:5120'
                ]

            ],

            $this->contactoRules(),

            $this->observacionRules()

        );

    }

}
