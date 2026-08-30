<?php

namespace App\Http\Requests\Transferencias;

use App\Http\Requests\BaseRequest;
use App\Models\Transferencias\TransferenciaInventario;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateTransferenciaInventarioRequest extends BaseRequest
{
    /**
     * Reglas para actualizar una transferencia.
     */
    public function rules(): array
    {
        return [

            'id_sucursal_origen' => [

                'sometimes',

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

                'sometimes',

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

            'observaciones' => [

                'sometimes',

                'nullable',

                'string'

            ],

            'usuario_modificacion' => [

                'sometimes',

                'nullable',

                'integer'

            ],

            'detalles' => [

                'sometimes',

                'required',

                'array',

                'min:1'

            ],

            'detalles.*.id_producto_variante' => [

                'required_with:detalles',

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

                'required_with:detalles',

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

            ],

            'fecha_envio' => [

                'prohibited'

            ],

            'fecha_recepcion' => [

                'prohibited'

            ],

            'fecha_rechazo' => [

                'prohibited'

            ],

            'id_usuario_envio' => [

                'prohibited'

            ],

            'id_usuario_recepcion' => [

                'prohibited'

            ],

            'id_usuario_rechazo' => [

                'prohibited'

            ]

        ];
    }

    /**
     * Validaciones adicionales.
     */
    public function after(): array
    {
        return [

            function (Validator $validator): void {

                $transferenciaInventario =
                    $this->route(
                        'transferenciaInventario'
                    );

                if (
                    ! $transferenciaInventario
                        instanceof TransferenciaInventario
                ) {

                    $transferenciaInventario =
                        TransferenciaInventario::findOrFail(

                            $transferenciaInventario

                        );

                }

                $idSucursalOrigen =
                    $this->input(

                        'id_sucursal_origen',

                        $transferenciaInventario
                            ->id_sucursal_origen

                    );

                $idSucursalDestino =
                    $this->input(

                        'id_sucursal_destino',

                        $transferenciaInventario
                            ->id_sucursal_destino

                    );

                if (
                    (int) $idSucursalOrigen
                    === (int) $idSucursalDestino
                ) {

                    $validator->errors()->add(

                        'id_sucursal_destino',

                        'La sucursal destino debe ser diferente de la sucursal origen.'

                    );

                }

                if (
                    $transferenciaInventario
                        ->estado_transferencia
                    !== TransferenciaInventario::PENDIENTE
                ) {

                    $validator->errors()->add(

                        'estado_transferencia',

                        'Solo pueden actualizarse transferencias pendientes.'

                    );

                }

            }

        ];
    }
}