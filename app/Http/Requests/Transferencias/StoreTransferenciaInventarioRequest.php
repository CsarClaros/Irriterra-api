<?php

namespace App\Http\Requests\Transferencias;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class StoreTransferenciaInventarioRequest extends BaseRequest
{
    /**
     * Reglas para registrar una transferencia.
     */
    public function rules(): array
    {
        return [

            'id_sucursal_origen' => [

                'required',

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

            'id_sucursal_destino' => [

                'required',

                'integer',

                'different:id_sucursal_origen',

                Rule::exists(
                    'sucursal',
                    'id_sucursal'
                )
                    ->where(
                        'estado_registro',
                        'A'
                    )

            ],

            'fecha_solicitud' => [

                'sometimes',

                'date',

                'before_or_equal:now'

            ],

            'id_usuario_solicitud' => [

                'required',

                'integer',

                Rule::exists(
                    'usuario',
                    'id_usuario'
                )
                    ->where(
                        'estado_registro',
                        'A'
                    )

            ],

            'observaciones' => [

                'nullable',

                'string'

            ],

            'usuario_creacion' => [

                'nullable',

                'integer'

            ],

            'detalles' => [

                'required',

                'array',

                'min:1'

            ],

            'detalles.*.id_producto_variante' => [

                'required',

                'integer',

                'distinct',

                Rule::exists(
                    'producto_variante',
                    'id_producto_variante'
                )
                    ->where(
                        'estado_registro',
                        'A'
                    )

            ],

            'detalles.*.cantidad' => [

                'required',

                'numeric',

                'decimal:0,3',

                'gt:0',

                'max:999999999999.999'

            ],

            'detalles.*.observaciones' => [

                'nullable',

                'string'

            ],

            'codigo_transferencia' => [

                'prohibited'

            ],

            'estado_transferencia' => [

                'prohibited'

            ]

        ];
    }
}