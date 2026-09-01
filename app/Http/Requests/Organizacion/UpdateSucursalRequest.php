<?php

namespace App\Http\Requests\Organizacion;

use App\Http\Requests\BaseRequest;
use App\Http\Requests\Traits\AuditoriaRules;
use App\Http\Requests\Traits\ContactoRules;
use App\Http\Requests\Traits\EstadoRegistroRules;
use App\Http\Requests\Traits\UbicacionRules;
use App\Http\Requests\Traits\ObservacionRules;
use Illuminate\Validation\Rule;
use App\Http\Requests\Traits\GoogleMapsRules;

class UpdateSucursalRequest extends BaseRequest
{
    use ContactoRules;
    use UbicacionRules;
    use EstadoRegistroRules;
    use AuditoriaRules;
    use ObservacionRules;
    use GoogleMapsRules;

    /**
     * Reglas para actualizar una sucursal.
     */
    public function rules(): array
    {
        $idSucursal = $this->route('sucursal') ?? $this->route('id');

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

                    Rule::unique('sucursal', 'codigo')
                        ->ignore($idSucursal, 'id_sucursal')

                ],

                'nombre' => [
                    'required',
                    'string',
                    'max:150'
                ],

                'url_maps' => [
                    'sometimes',
                    'nullable',
                    'url',
                    'max:2000'
                ],

                'url_maps_embed' =>
                    array_merge(
                        [
                            'sometimes'
                        ],
                        $this->reglasMapsEmbed()
                    ),

            ],

            $this->ubicacionRules(),

            $this->contactoRules(),

            $this->observacionRules(),

        // $this->auditoriaRules(),

        // $this->estadoRegistroRules()

        );
    }

    protected function prepareForValidation():
    void
    {

        if (
            $this->has(
                'url_maps_embed'
            )
        ) {

            $this->merge([

                'url_maps_embed' =>
                    $this->normalizarMapsEmbed(

                        $this->input(
                            'url_maps_embed'
                        )

                    )

            ]);

        }

    }
}
