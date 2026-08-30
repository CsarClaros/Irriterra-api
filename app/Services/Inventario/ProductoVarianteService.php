<?php

namespace App\Services\Inventario;

use App\Models\Inventario\ProductoVariante;
use App\Repositories\Inventario\ProductoVarianteRepository;
use Illuminate\Support\Facades\DB;

class ProductoVarianteService
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly ProductoVarianteRepository $repository
    ) {
    }

    /**
     * Lista variantes activas.
     */
    public function index()
    {
        return $this->repository->getAll();
    }

    /**
     * Muestra una variante.
     */
    public function show(int $id): ProductoVariante
    {
        return $this->repository->findById($id);
    }

    /**
     * Registra una variante.
     */
    public function store(array $data): ProductoVariante
    {
        return DB::transaction(function () use ($data) {

            $data['estado_registro'] = 'A';

            return $this->repository->create($data);

        });
    }

    /**
     * Actualiza una variante.
     */
    public function update(
        ProductoVariante $productoVariante,
        array $data
    ): ProductoVariante {

        return DB::transaction(
            function () use ($productoVariante, $data) {

                return $this->repository->update(

                    $productoVariante,

                    $data

                );

            }
        );
    }

    /**
     * Eliminación lógica.
     */
    public function destroy(
        ProductoVariante $productoVariante
    ): bool {

        return DB::transaction(
            function () use ($productoVariante) {

                return $this->repository->delete(

                    $productoVariante

                );

            }
        );
    }
}