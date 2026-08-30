<?php

namespace App\Http\Resources\Inventario;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
{
    /**
     * Transforma el recurso.
     */
    public function toArray(Request $request): array
    {
        return [

            'id_producto' => $this->id_producto,

            'id_categoria' => $this->id_categoria,

            'nombre' => $this->nombre,

            'marca' => $this->marca,

            'modelo' => $this->modelo,

            'descripcion' => $this->descripcion,

            'catalogo_pdf' => $this->catalogo_pdf,

            'observaciones' => $this->observaciones,

            'estado_registro' => $this->estado_registro,

            'usuario_creacion' => $this->usuario_creacion,

            'usuario_modificacion' => $this->usuario_modificacion,

            'categoria' => new CategoriaResource(

                $this->whenLoaded('categoria')

            ),

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at

        ];
    }
}