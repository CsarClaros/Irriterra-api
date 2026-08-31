<?php

namespace App\Http\Resources\Inventario;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductoImagenResource extends JsonResource
{
    /**
     * Transforma el recurso.
     */
    public function toArray(Request $request): array
    {
        return [

            'id_producto_imagen' =>
                $this->id_producto_imagen,

            'id_producto' => $this->id_producto,

            'ruta_imagen' =>
                $this->resolverRutaImagen(
                    $this->ruta_imagen
                ),

            'texto_alternativo' =>
                $this->texto_alternativo,

            'orden' => $this->orden,

            'es_principal' => $this->es_principal,

            'observaciones' => $this->observaciones,

            'estado_registro' => $this->estado_registro,

            'usuario_creacion' => $this->usuario_creacion,

            'usuario_modificacion' =>
                $this->usuario_modificacion,

            'producto' => new ProductoResource(

                $this->whenLoaded('producto')

            ),

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at

        ];
    }

    /**
     * Resuelve la URL pública de la imagen.
     */
    private function resolverRutaImagen(
        ?string $ruta
    ): ?string {

        if (
            !$ruta
        ) {

            return null;

        }


        /*
         * Las nuevas imágenes administradas
         * por Laravel están en storage.
         */

        if (
            str_starts_with(
                $ruta,
                'productos/'
            )
        ) {

            return Storage::disk(
                'public'
            )
                ->url(
                    $ruta
                );

        }


        /*
         * Compatibilidad con rutas anteriores:
         *
         * /assets/...
         * assets/...
         * /storage/...
         * URL externas.
         */

        return $ruta;
    }
}
