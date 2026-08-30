<?php

namespace App\Http\Resources\Transferencias;

use App\Http\Resources\Organizacion\SucursalResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferenciaInventarioResource extends JsonResource
{
    /**
     * Transforma la transferencia.
     */
    public function toArray(
        Request $request
    ): array {

        return [

            'id_transferencia_inventario' =>
                $this->id_transferencia_inventario,

            'codigo_transferencia' =>
                $this->codigo_transferencia,

            'id_sucursal_origen' =>
                $this->id_sucursal_origen,

            'id_sucursal_destino' =>
                $this->id_sucursal_destino,

            'estado_transferencia' =>
                $this->estado_transferencia,

            'fecha_solicitud' =>
                $this->fecha_solicitud,

            'fecha_envio' =>
                $this->fecha_envio,

            'fecha_recepcion' =>
                $this->fecha_recepcion,

            'fecha_rechazo' =>
                $this->fecha_rechazo,

            'id_usuario_solicitud' =>
                $this->id_usuario_solicitud,

            'id_usuario_envio' =>
                $this->id_usuario_envio,

            'id_usuario_recepcion' =>
                $this->id_usuario_recepcion,

            'id_usuario_rechazo' =>
                $this->id_usuario_rechazo,

            'motivo_rechazo' =>
                $this->motivo_rechazo,

            'observaciones' =>
                $this->observaciones,

            'estado_registro' =>
                $this->estado_registro,

            'usuario_creacion' =>
                $this->usuario_creacion,

            'usuario_modificacion' =>
                $this->usuario_modificacion,

            'sucursal_origen' =>
                new SucursalResource(

                    $this->whenLoaded(
                        'sucursalOrigen'
                    )

                ),

            'sucursal_destino' =>
                new SucursalResource(

                    $this->whenLoaded(
                        'sucursalDestino'
                    )

                ),

            'detalles' =>
                TransferenciaInventarioDetalleResource::collection(

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