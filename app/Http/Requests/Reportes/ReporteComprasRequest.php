<?php

namespace App\Http\Requests\Reportes;

use App\Http\Requests\BaseRequest;
use App\Models\Compras\Compra;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ReporteComprasRequest extends BaseRequest
{
    /**
     * Reglas para reportes de compras.
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

            ],

            'id_proveedor' => [

                'sometimes',

                'nullable',

                'integer',

                Rule::exists(
                    'proveedor',
                    'id_proveedor'
                )

            ],

            'id_usuario_comprador' => [

                'sometimes',

                'nullable',

                'integer',

                Rule::exists(
                    'usuario',
                    'id_usuario'
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

            'estado_compra' => [

                'sometimes',

                'nullable',

                'string',

                Rule::in(
                    Compra::ESTADOS
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