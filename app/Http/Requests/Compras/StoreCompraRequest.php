<?php

namespace App\Http\Requests\Compras;

use App\Http\Requests\BaseRequest;
use App\Models\Compras\Compra;
use Illuminate\Validation\Rule;

class StoreCompraRequest extends BaseRequest
{
    /**
     * Normaliza campos antes de validar.
     */
    protected function prepareForValidation(): void
    {
        $moneda =
            strtoupper(
                trim(
                    (string)$this->input(
                        'moneda',
                        Compra::MONEDA_BOB
                    )
                )
            );

        $detalles =
            $this->input(
                'detalles'
            );

        /*
         * Compatibilidad temporal con el frontend
         * anterior.
         *
         * Si todavía envía costo_unitario y descuento,
         * los interpretamos como importes en la moneda
         * original seleccionada.
         */
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

                /*
                 * Estos campos serán calculados
                 * exclusivamente por el backend.
                 */
                unset(

                    $detalle['costo_unitario'],

                    $detalle['descuento'],

                    $detalle['subtotal'],

                    $detalle['subtotal_original']

                );

                $detalles[$indice] =
                    $detalle;
            }
        }

        $this->merge([

            'numero_factura' =>

                $this->numero_factura === ''

                    ? null

                    : $this->numero_factura,

            'moneda' =>
                $moneda,

            'detalles' =>
                $detalles

        ]);
    }

    /**
     * Reglas para registrar una compra.
     */
    public function rules(): array
    {
        return [

            'id_sucursal' => [

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

                'date',

                'before_or_equal:now'

            ],

            'numero_factura' => [

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

                            $this->input(
                                'id_proveedor'
                            )

                        )

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

                'nullable',

                'string'

            ],

            'usuario_creacion' => [

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
                'integer',
                'min:1',
            ],

            /*
             * El usuario registra este valor
             * en la moneda original de la compra.
             */
            'detalles.*.costo_unitario_original' => [

                'required',

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
            | Campos controlados por el servidor
            |--------------------------------------------------------------------------
            */

            'codigo_compra' => [
                'prohibited'
            ],

            'estado_compra' => [
                'prohibited'
            ],

            'fecha_confirmacion' => [
                'prohibited'
            ],

            'fecha_recepcion' => [
                'prohibited'
            ],

            'fecha_anulacion' => [
                'prohibited'
            ],

            'subtotal' => [
                'prohibited'
            ],

            'descuento' => [
                'prohibited'
            ],

            'total' => [
                'prohibited'
            ],

            'subtotal_original' => [
                'prohibited'
            ],

            'descuento_original' => [
                'prohibited'
            ],

            'total_original' => [
                'prohibited'
            ],

            'origen_tipo_cambio' => [
                'prohibited'
            ],

            'fuente_tipo_cambio' => [
                'prohibited'
            ],

            'fecha_tipo_cambio' => [
                'prohibited'
            ],

            'id_usuario_confirmacion' => [
                'prohibited'
            ],

            'id_usuario_recepcion' => [
                'prohibited'
            ],

            'id_usuario_anulacion' => [
                'prohibited'
            ],

            'motivo_anulacion' => [
                'prohibited'
            ],

            'estado_registro' => [
                'prohibited'
            ],

            'usuario_modificacion' => [
                'prohibited'
            ],

            'detalles.*.costo_unitario' => [
                'prohibited'
            ],

            'detalles.*.descuento' => [
                'prohibited'
            ],

            'detalles.*.subtotal' => [
                'prohibited'
            ],

            'detalles.*.subtotal_original' => [
                'prohibited'
            ]

        ];
    }
}
