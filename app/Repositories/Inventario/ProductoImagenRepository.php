<?php

namespace App\Repositories\Inventario;

use App\Models\Inventario\ProductoImagen;

class ProductoImagenRepository
{
    /**
     * Lista imágenes activas.
     */
    public function getAll()
    {
        return ProductoImagen::with([

            'producto.categoria'

        ])
            ->where('estado_registro', 'A')
            ->orderBy('id_producto')
            ->orderBy('orden')
            ->get();
    }

    /**
     * Busca por ID.
     */
    public function findById(int $id): ProductoImagen
    {
        return ProductoImagen::with([

            'producto.categoria'

        ])
            ->findOrFail($id);
    }

    /**
     * Registra una imagen.
     */
    public function create(array $data): ProductoImagen
    {
        $productoImagen = ProductoImagen::create($data);

        return $productoImagen->load([

            'producto.categoria'

        ]);
    }

    /**
     * Actualiza una imagen.
     */
    public function update(
        ProductoImagen $productoImagen,
        array $data
    ): ProductoImagen {

        $productoImagen->update($data);

        return $productoImagen->fresh([

            'producto.categoria'

        ]);
    }

    /**
     * Desmarca las imágenes principales de un producto.
     */
    public function desmarcarPrincipal(
        int $idProducto,
        ?int $exceptoId = null
    ): int {

        $query = ProductoImagen::where(
            'id_producto',
            $idProducto
        )
            ->where('es_principal', true);

        if ($exceptoId !== null) {

            $query->where(
                'id_producto_imagen',
                '<>',
                $exceptoId
            );

        }

        return $query->update([

            'es_principal' => false

        ]);
    }

    /**
     * Eliminación lógica.
     */
    public function delete(
        ProductoImagen $productoImagen
    ): bool {

        return $productoImagen->update([

            'estado_registro' => 'I'

        ]);
    }
}