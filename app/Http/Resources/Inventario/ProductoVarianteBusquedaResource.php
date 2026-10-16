<?php

namespace App\Http\Resources\Inventario;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class ProductoVarianteBusquedaResource extends JsonResource
{
    public function toArray(
        Request $request
    ): array
    {
        return [

            'id_producto_variante' =>
                $this->id_producto_variante,

            'producto' =>
                $this->producto
                    ?->nombre
                ?? '',

            'variante' =>
                $this->nombre,

            'sku' =>
                $this->sku,

            'codigo_comercial' =>
                $this->codigo_comercial,

            'unidad_medida' =>
                $this->unidad_medida,

            'modelo' =>
                $this->producto
                    ?->modelo,

            /*
             * La marca normalizada de la variante
             * tiene prioridad.
             *
             * Se mantiene temporalmente el fallback
             * al campo antiguo producto.marca.
             */
            'marca' =>
                $this->marca
                    ?->nombre
                ??
                $this->producto
                    ?->marca

        ];
    }
}
