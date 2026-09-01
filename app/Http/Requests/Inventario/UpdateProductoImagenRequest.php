<?php

namespace App\Http\Requests\Inventario;

use App\Http\Requests\BaseRequest;
use App\Models\Inventario\ProductoImagen;
use Illuminate\Validation\Rule;

class UpdateProductoImagenRequest extends BaseRequest
{
    /**
     * Reglas para actualizar una imagen.
     */
    public function rules(): array
    {
        /*
        |--------------------------------------------------------------------------
        | Imagen actual
        |--------------------------------------------------------------------------
        */

        $productoImagen =
            $this->route(
                'productoImagen'
            );


        /*
        |--------------------------------------------------------------------------
        | ID imagen
        |--------------------------------------------------------------------------
        */

        $idProductoImagen =

            $productoImagen
            instanceof ProductoImagen

                ? $productoImagen
                ->id_producto_imagen

                : $productoImagen;


        /*
        |--------------------------------------------------------------------------
        | Producto efectivo
        |--------------------------------------------------------------------------
        |
        | Si el frontend envía id_producto,
        | utilizamos ese valor.
        |
        | Si no lo envía, utilizamos el producto
        | de la imagen actualmente registrada.
        |
        */

        $idProducto =

            $this->input(
                'id_producto'
            )

            ?? (

        $productoImagen
        instanceof ProductoImagen

            ? $productoImagen
            ->id_producto

            : null

        );


        return [

            /*
            |--------------------------------------------------------------------------
            | Producto
            |--------------------------------------------------------------------------
            */

            'id_producto' => [

                'sometimes',

                'required',

                'integer',

                Rule::exists(
                    'producto',
                    'id_producto'
                )
                    ->where(
                        'estado_registro',
                        'A'
                    )

            ],


            /*
            |--------------------------------------------------------------------------
            | Variante
            |--------------------------------------------------------------------------
            */

            'id_producto_variante' => [

                'sometimes',

                'nullable',

                'integer',

                Rule::exists(
                    'producto_variante',
                    'id_producto_variante'
                )
                    ->where(
                        function (
                            $query
                        ) use (
                            $idProducto
                        ) {

                            $query
                                ->where(
                                    'id_producto',
                                    $idProducto
                                )
                                ->where(
                                    'estado_registro',
                                    'A'
                                );

                        }
                    )

            ],


            /*
            |--------------------------------------------------------------------------
            | Archivo
            |--------------------------------------------------------------------------
            */

            'imagen' => [

                'sometimes',

                'nullable',

                'file',

                'image',

                'mimes:jpg,jpeg,png,webp',

                'max:5120'

            ],


            /*
            |--------------------------------------------------------------------------
            | Texto alternativo
            |--------------------------------------------------------------------------
            */

            'texto_alternativo' => [

                'sometimes',

                'nullable',

                'string',

                'max:255'

            ],


            /*
            |--------------------------------------------------------------------------
            | Orden
            |--------------------------------------------------------------------------
            */

            'orden' => [

                'sometimes',

                'required',

                'integer',

                'min:1'

            ],


            /*
            |--------------------------------------------------------------------------
            | Principal
            |--------------------------------------------------------------------------
            */

            'es_principal' => [

                'sometimes',

                'required',

                'boolean'

            ],


            /*
            |--------------------------------------------------------------------------
            | Observaciones
            |--------------------------------------------------------------------------
            */

            'observaciones' => [

                'sometimes',

                'nullable',

                'string'

            ],


            /*
            |--------------------------------------------------------------------------
            | Auditoría
            |--------------------------------------------------------------------------
            */

            'usuario_modificacion' => [

                'sometimes',

                'nullable',

                'integer'

            ]

        ];
    }


    /**
     * Mensajes personalizados.
     */
    public function messages(): array
    {
        return [

            'imagen.image' =>
                'El archivo seleccionado debe ser una imagen.',

            'imagen.mimes' =>
                'La imagen debe estar en formato JPG, JPEG, PNG o WEBP.',

            'imagen.max' =>
                'La imagen no puede superar los 5 MB.',

            'id_producto_variante.exists' =>
                'La variante seleccionada no pertenece al producto o no está activa.'

        ];
    }
}
