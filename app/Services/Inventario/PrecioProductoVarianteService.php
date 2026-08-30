<?php

namespace App\Services\Inventario;

use App\Models\Inventario\PrecioProductoVariante;
use App\Repositories\Inventario\PrecioProductoVarianteRepository;
use Illuminate\Support\Facades\DB;

class PrecioProductoVarianteService
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly PrecioProductoVarianteRepository $repository
    ) {
    }

    /**
     * Lista precios activos.
     */
    public function index()
    {
        return $this->repository->getAll();
    }

    /**
     * Muestra una configuración de precios.
     */
    public function show(
        int $id
    ): PrecioProductoVariante {

        return $this->repository->findById($id);
    }

    /**
     * Registra precios para una variante.
     */
    public function store(
        array $data
    ): PrecioProductoVariante {

        return DB::transaction(
            function () use ($data) {

                $data['fecha_vigencia'] =
                    $data['fecha_vigencia'] ?? now();

                $data['estado_registro'] = 'A';

                return $this->repository->create($data);

            }
        );
    }

    /**
     * Actualiza precios de una variante.
     */
    public function update(
        PrecioProductoVariante $precioProductoVariante,
        array $data
    ): PrecioProductoVariante {

        return DB::transaction(
            function () use (
                $precioProductoVariante,
                $data
            ) {

                return $this->repository->update(

                    $precioProductoVariante,

                    $data

                );

            }
        );
    }

    /**
     * Eliminación lógica.
     */
    public function destroy(
        PrecioProductoVariante $precioProductoVariante
    ): bool {

        return DB::transaction(
            function () use (
                $precioProductoVariante
            ) {

                return $this->repository->delete(

                    $precioProductoVariante

                );

            }
        );
    }
}