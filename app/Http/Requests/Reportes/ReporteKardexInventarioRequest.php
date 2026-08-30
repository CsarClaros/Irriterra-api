<?php

namespace App\Http\Requests\Reportes;

use App\Http\Requests\BaseRequest;
use App\Models\Inventario\MovimientoInventario;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ReporteKardexInventarioRequest extends BaseRequest
{
    /**
     * Reglas para consultar el kardex.
     */
    public function rules(): array
    {
        return [

            'id_sucursal' => [

                'sometimes',

                'nullable',

                'integer',

                Rule::exists(
                    'sucursal',
                    'id_sucursal'
                )
                    ->where(
                        'estado_registro',
                        'A'
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
                    ->where(
                        'estado_registro',
                        'A'
                    )

            ],

            'tipo_movimiento' => [

                'sometimes',

                'nullable',

                'string',

                Rule::in(
                    MovimientoInventario::TIPOS
                )

            ],

            'tipo_referencia' => [

                'sometimes',

                'nullable',

                'string',

                Rule::in([

                    'VENTA',

                    'COMPRA',

                    'TRANSFERENCIA',

                    'AJUSTE_INICIAL',

                    'AJUSTE_PRUEBA',

                    'AJUSTE_MANUAL'

                ])

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