<?php

namespace App\Http\Requests\Compras;

use App\Http\Requests\BaseRequest;
use App\Models\Compras\Compra;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateCompraRequest extends BaseRequest
{
    /**
     * Normaliza campos opcionales.
     */
    protected function prepareForValidation(): void
    {
        $compra =
            $this->route(
                'compra'
            );

        if (
            !$compra instanceof Compra
            &&
            $compra !== null
        ) {

            $compra =
                Compra::find(
                    $compra
                );
        }

        $monedaActual =
            $compra?->moneda
            ?? Compra::MONEDA_BOB;

        $moneda =
            strtoupper(
                trim(
                    (string)$this->input(
                        'moneda',
                        $monedaActual
                    )
                )
            );

        $merge = [

            'moneda' =>
                $moneda

        ];

        if (
            $this->exists(
                'numero_factura'
            )
        ) {

            $merge['numero_factura'] =
                $this->numero_factura === ''

                    ? null

                    : $this->numero_factura;
        }

        /*
         * Una compra USD ya existente conserva
         * su tipo de cambio si el cliente no
         * vuelve a enviarlo.
         */
        if (
            $moneda === Compra::MONEDA_USD
            &&
            !$this->exists(
                'tipo_cambio_aplicado'
            )
            &&
            $compra
            &&
            $compra->moneda
            === Compra::MONEDA_USD
        ) {

            $merge['tipo_cambio_aplicado'] =
                $compra
                    ->tipo_cambio_aplicado;
        }

        if (
            $moneda === Compra::MONEDA_USD
            &&
            !$this->exists(
                'tipo_cambio_referencia'
            )
            &&
            $compra
            &&
            $compra
                ->tipo_cambio_referencia
            !== null
        ) {

            $merge['tipo_cambio_referencia'] =
                $compra
                    ->tipo_cambio_referencia;
        }

        $detalles =
            $this->input(
                'detalles'
            );

        if (
            is_array(
                $detalles
            )
        ) {

            foreach (
                $detalles as $indice => $detalle
            ) {

                if (
                    !is_array(
                        $detalle
                    )
                ) {

                    continue;
                }

                if (
                    !array_key_exists(
                        'costo_unitario_original',
                        $detalle
                    )
                    &&
                    array_key_exists(
                        'costo_unitario',
                        $detalle
                    )
                ) {

                    $detalle['costo_unitario_original'] =
                        $detalle['costo_unitario'];
                }

                if (
                    !array_key_exists(
                        'descuento_original',
                        $detalle
                    )
                    &&
                    array_key_exists(
                        'descuento',
                        $detalle
                    )
                ) {

                    $detalle['descuento_original'] =
                        $detalle['descuento'];
                }

                $detalle['descuento_original'] =
                    $detalle['descuento_original']
                    ?? 0;

                unset(

                    $detalle['costo_unitario'],

                    $detalle['descuento'],

                    $detalle['subtotal'],

                    $detalle['subtotal_original']

                );

                $detalles[$indice] =
                    $detalle;
            }

            $merge['detalles'] =
                $detalles;
        }

        $this->merge(
            $merge
        );
    }

    public function rules(): array
    {
        $compra =
            $this->route(
                'compra'
            );

        if (
            !$compra instanceof Compra
        ) {

            $compra =
                Compra::findOrFail(
                    $compra
                );
        }

        $idProveedor =
            $this->input(

                'id_proveedor',

                $compra->id_proveedor

            );

        return [

            'id_sucursal' => [

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

            'id_proveedor' => [

                'sometimes',

                'required',

                'integer',

                Rule::exists(
                    'proveedor',
                    'id_proveedor'
                )
                    ->where(
                        'estado_registro',
                        'A'
                    )

            ],

            'id_usuario_comprador' => [

                'sometimes',

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

            'fecha_compra' => [

                'sometimes',

                'required',

                'date',

                'before_or_equal:now'

            ],

            'numero_factura' => [

                'sometimes',

                'nullable',

                'string',

                'max:50',

                Rule::unique(
                    'compra',
                    'numero_factura'
                )
                    ->where(

                        fn($query) => $query->where(

                            'id_proveedor',

                            $idProveedor

                        )

                    )
                    ->ignore(

                        $compra->id_compra,

                        'id_compra'

                    )

            ],

            /*
            |--------------------------------------------------------------------------
            | Moneda
            |--------------------------------------------------------------------------
            */

            'moneda' => [

                'required',

                'string',

                Rule::in(
                    Compra::MONEDAS
                )

            ],

            'tipo_cambio_referencia' => [

                'prohibited_unless:moneda,USD',

                'nullable',

                'numeric',

                'decimal:0,6',

                'gt:0',

                'max:999999999.999999'

            ],

            'tipo_cambio_aplicado' => [

                'required_if:moneda,USD',

                'prohibited_unless:moneda,USD',

                'nullable',

                'numeric',

                'decimal:0,6',

                'gt:0',

                'max:999999999.999999'

            ],

            'observaciones' => [

                'sometimes',

                'nullable',

                'string'

            ],

            'usuario_modificacion' => [

                'sometimes',

                'nullable',

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

            /*
            |--------------------------------------------------------------------------
            | Detalles
            |--------------------------------------------------------------------------
            */

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
                'required',
                'integer',
                'min:1',
            ],

            'detalles.*.costo_unitario_original' => [

                'required_with:detalles',

                'numeric',

                'decimal:0,2',

                'gt:0',

                'max:9999999999999.99'

            ],

            'detalles.*.descuento_original' => [

                'sometimes',

                'numeric',

                'decimal:0,2',

                'min:0',

                'max:9999999999999.99'

            ],

            'detalles.*.observaciones' => [

                'nullable',

                'string'

            ],

            /*
            |--------------------------------------------------------------------------
            | Campos protegidos
            |--------------------------------------------------------------------------
            */

            'codigo_compra' => ['prohibited'],

            'estado_compra' => ['prohibited'],

            'fecha_confirmacion' => ['prohibited'],

            'fecha_recepcion' => ['prohibited'],

            'fecha_anulacion' => ['prohibited'],

            'subtotal' => ['prohibited'],

            'descuento' => ['prohibited'],

            'total' => ['prohibited'],

            'subtotal_original' => ['prohibited'],

            'descuento_original' => ['prohibited'],

            'total_original' => ['prohibited'],

            'origen_tipo_cambio' => ['prohibited'],

            'fuente_tipo_cambio' => ['prohibited'],

            'fecha_tipo_cambio' => ['prohibited'],

            'id_usuario_confirmacion' => ['prohibited'],

            'id_usuario_recepcion' => ['prohibited'],

            'id_usuario_anulacion' => ['prohibited'],

            'motivo_anulacion' => ['prohibited'],

            'estado_registro' => ['prohibited'],

            'usuario_creacion' => ['prohibited'],

            'detalles.*.costo_unitario' => ['prohibited'],

            'detalles.*.descuento' => ['prohibited'],

            'detalles.*.subtotal' => ['prohibited'],

            'detalles.*.subtotal_original' => ['prohibited']

        ];
    }

    /**
     * Solo pueden editarse compras en borrador.
     */
    public function after(): array
    {
        return [

            function (
                Validator $validator
            ): void {

                $compra =
                    $this->route(
                        'compra'
                    );

                if (
                    !$compra instanceof Compra
                ) {

                    $compra =
                        Compra::findOrFail(
                            $compra
                        );
                }

                if (
                    $compra->estado_compra
                    !== Compra::BORRADOR
                ) {

                    $validator
                        ->errors()
                        ->add(

                            'estado_compra',

                            'Solo pueden actualizarse compras en borrador.'

                        );
                }
            }

        ];
    }
}
