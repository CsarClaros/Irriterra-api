<?php

namespace App\Http\Requests\Reportes;

use App\Http\Requests\BaseRequest;
use App\Models\Transferencias\TransferenciaInventario;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ReporteTransferenciasRequest extends BaseRequest
{
    /**
     * Reglas para reportes de transferencias.
     */
    public function rules(): array
    {
        return [

            'id_sucursal_origen' => [

                'sometimes',

                'nullable',

                'integer',

                Rule::exists(
                    'sucursal',
                    'id_sucursal'
                )

            ],

            'id_sucursal_destino' => [

                'sometimes',

                'nullable',

                'integer',

                Rule::exists(
                    'sucursal',
                    'id_sucursal'
                )

            ],

            'id_producto_variante' => [

                'sometimes',

                'nullable',

                'integer',

                Rule::exists(
                    'producto_variante',
                    'id_producto_variante'
                )

            ],

            'estado_transferencia' => [

                'sometimes',

                'nullable',

                'string',

                Rule::in(
                    TransferenciaInventario::ESTADOS
                )

            ],

            'fecha_desde' => [

                'sometimes',

                'nullable',

                'date'

            ],

            'fecha_hasta' => [

                'sometimes',

                'nullable',

                'date'

            ],

            'limite' => [

                'sometimes',

                'integer',

                'min:1',

                'max:100'

            ]

        ];
    }

    /**
     * Validaciones adicionales.
     */
    public function after(): array
    {
        return [

            function (
                Validator $validator
            ): void {

                if (
                    $this->filled('fecha_desde')
                    && $this->filled('fecha_hasta')
                    && $this->date('fecha_desde')
                        ->greaterThan(
                            $this->date('fecha_hasta')
                        )
                ) {

                    $validator->errors()->add(

                        'fecha_hasta',

                        'La fecha final debe ser igual o posterior a la fecha inicial.'

                    );

                }

            }

        ];
    }
}