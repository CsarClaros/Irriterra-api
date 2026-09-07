<?php

namespace App\Http\Requests\Organizacion;

use App\Http\Requests\BaseRequest;
use App\Http\Requests\Traits\AuditoriaRules;
use App\Http\Requests\Traits\ContactoRules;
use App\Http\Requests\Traits\EstadoRegistroRules;
use App\Http\Requests\Traits\UbicacionRules;
use App\Http\Requests\Traits\GoogleMapsRules;
use Illuminate\Validation\Rule;

class StoreSucursalRequest extends BaseRequest
{
    use ContactoRules;
    use UbicacionRules;
    use EstadoRegistroRules;
    use AuditoriaRules;
    use GoogleMapsRules;

    /**
     * Reglas para registrar una sucursal.
     */
    public function rules(): array
    {
        return array_merge(

            [

                'id_empresa' => [

                    'required',

                    'integer',

                    Rule::exists(
                        'empresa',
                        'id_empresa'
                    )
                        ->where(
                            'estado_registro',
                            'A'
                        )

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
                ],

                'url_maps' => [
                    'nullable',
                    'url',
                    'max:2000'
                ],

                'url_maps_embed' =>
                    $this->reglasMapsEmbed(),

            ],

            $this->ubicacionRules(),

            $this->contactoRules(),

        // $this->auditoriaRules(),

        // $this->estadoRegistroRules()

        );
    }

    /*
|--------------------------------------------------------------------------
| Preparar datos
|--------------------------------------------------------------------------
*/

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
