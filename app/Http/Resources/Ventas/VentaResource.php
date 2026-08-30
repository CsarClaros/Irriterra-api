<?php

namespace App\Http\Resources\Ventas;

use App\Http\Resources\Organizacion\SucursalResource;
use App\Http\Resources\Seguridad\UsuarioResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VentaResource extends JsonResource
{
    /**
     * Transforma una venta.
     */
    public function toArray(
        Request $request
    ): array {

        return [

            'id_venta' =>
                $this->id_venta,

            'codigo_venta' =>
                $this->codigo_venta,

            'id_sucursal' =>
                $this->id_sucursal,

            'id_cliente' =>
                $this->id_cliente,

            'id_usuario_vendedor' =>
                $this->id_usuario_vendedor,

            'estado_venta' =>
                $this->estado_venta,

            'fecha_venta' =>
                $this->fecha_venta,

            'fecha_pago' =>
                $this->fecha_pago,

            'fecha_anulacion' =>
                $this->fecha_anulacion,

            'subtotal' =>
                $this->subtotal,

            'descuento' =>
                $this->descuento,

            'total' =>
                $this->total,

            'monto_pagado' =>
                $this->monto_pagado,

            'metodo_pago' =>
                $this->metodo_pago,

            'motivo_anulacion' =>
                $this->motivo_anulacion,

            'id_usuario_anulacion' =>
                $this->id_usuario_anulacion,

            'observaciones' =>
                $this->observaciones,

            'estado_registro' =>
                $this->estado_registro,

            'usuario_creacion' =>
                $this->usuario_creacion,

            'usuario_modificacion' =>
                $this->usuario_modificacion,

            'sucursal' =>
                new SucursalResource(

                    $this->whenLoaded(
                        'sucursal'
                    )

                ),

            'cliente' =>
                new ClienteResource(

                    $this->whenLoaded(
                        'cliente'
                    )

                ),

            'vendedor' =>
                new UsuarioResource(

                    $this->whenLoaded(
                        'vendedor'
                    )

                ),

            'detalles' =>
                VentaDetalleResource::collection(

                    $this->whenLoaded(
                        'detalles'
                    )

                ),

            'created_at' =>
                $this->created_at,

            'updated_at' =>
                $this->updated_at

        ];
    }
}