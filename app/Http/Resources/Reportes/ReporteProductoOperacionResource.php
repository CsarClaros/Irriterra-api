<?php

namespace App\Http\Resources\Reportes;

use App\Http\Resources\Inventario\ProductoVarianteResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReporteProductoOperacionResource extends JsonResource
{
    /**
     * Transforma un agregado por producto.
     */
    public function toArray(
        Request $request
    ): array {

        return [

            'id_producto_variante' =>
                $this[
                    'id_producto_variante'
                ],

            'cantidad' =>
                $this['cantidad'],

            'total' =>
                $this['total'] ?? null,

            'operaciones' =>
                $this['operaciones'],

            'producto_variante' =>
                new ProductoVarianteResource(

                    $this[
                        'producto_variante'
                    ]

                )

        ];
    }
}