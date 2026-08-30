<?php

namespace App\Services\Inventario;

use App\Models\Inventario\ProductoImagen;
use App\Repositories\Inventario\ProductoImagenRepository;
use Illuminate\Support\Facades\DB;

class ProductoImagenService
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly ProductoImagenRepository $repository
    ) {
    }

    /**
     * Lista imágenes activas.
     */
    public function index()
    {
        return $this->repository->getAll();
    }

    /**
     * Muestra una imagen.
     */
    public function show(int $id): ProductoImagen
    {
        return $this->repository->findById($id);
    }

    /**
     * Registra una imagen.
     */
    public function store(array $data): ProductoImagen
    {
        return DB::transaction(function () use ($data) {

            $data['estado_registro'] = 'A';

            if (
                isset($data['es_principal'])
                && $data['es_principal'] === true
            ) {

                $this->repository->desmarcarPrincipal(

                    $data['id_producto']

                );

            }

            return $this->repository->create($data);

        });
    }

    /**
     * Actualiza una imagen.
     */
    public function update(
        ProductoImagen $productoImagen,
        array $data
    ): ProductoImagen {

        return DB::transaction(
            function () use ($productoImagen, $data) {

                $idProducto = $data['id_producto']
                    ?? $productoImagen->id_producto;

                $esPrincipal = array_key_exists(
                    'es_principal',
                    $data
                )
                    ? (bool) $data['es_principal']
                    : (bool) $productoImagen->es_principal;

                if ($esPrincipal) {

                    $this->repository->desmarcarPrincipal(

                        $idProducto,

                        $productoImagen->id_producto_imagen

                    );

                }

                return $this->repository->update(

                    $productoImagen,

                    $data

                );

            }
        );
    }

    /**
     * Eliminación lógica.
     */
    public function destroy(
        ProductoImagen $productoImagen
    ): bool {

        return DB::transaction(
            function () use ($productoImagen) {

                return $this->repository->delete(

                    $productoImagen

                );

            }
        );
    }
}