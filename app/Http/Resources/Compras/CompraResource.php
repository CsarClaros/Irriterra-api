<?php

namespace App\Http\Resources\Compras;

use App\Http\Resources\Organizacion\SucursalResource;
use App\Http\Resources\Seguridad\UsuarioResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompraResource extends JsonResource
{
    /**
     * Transforma una compra.
     */
    public function toArray(
        Request $request
    ): array {

        return [

            'id_compra' =>
                $this->id_compra,

            'codigo_compra' =>
                $this->codigo_compra,

            'id_sucursal' =>
                $this->id_sucursal,

            'id_proveedor' =>
                $this->id_proveedor,

            'id_usuario_comprador' =>
                $this->id_usuario_comprador,

            'estado_compra' =>
                $this->estado_compra,

            'fecha_compra' =>
                $this->fecha_compra,

            'fecha_confirmacion' =>
                $this->fecha_confirmacion,

            'fecha_recepcion' =>
                $this->fecha_recepcion,

            'fecha_anulacion' =>
                $this->fecha_anulacion,

            'numero_factura' =>
                $this->numero_factura,

            'subtotal' =>
                $this->subtotal,

            'descuento' =>
                $this->descuento,

            'total' =>
                $this->total,

            'id_usuario_confirmacion' =>
                $this->id_usuario_confirmacion,

            'id_usuario_recepcion' =>
                $this->id_usuario_recepcion,

            'id_usuario_anulacion' =>
                $this->id_usuario_anulacion,

            'motivo_anulacion' =>
                $this->motivo_anulacion,

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

            'proveedor' =>
                new ProveedorResource(

                    $this->whenLoaded(
                        'proveedor'
                    )

                ),

            'comprador' =>
                new UsuarioResource(

                    $this->whenLoaded(
                        'comprador'
                    )

                ),

            'usuario_confirmacion' =>
                new UsuarioResource(

                    $this->whenLoaded(
                        'usuarioConfirmacion'
                    )

                ),

            'usuario_recepcion' =>
                new UsuarioResource(

                    $this->whenLoaded(
                        'usuarioRecepcion'
                    )

                ),

            'usuario_anulacion' =>
                new UsuarioResource(

                    $this->whenLoaded(
                        'usuarioAnulacion'
                    )

                ),

            'detalles' =>
                CompraDetalleResource::collection(

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