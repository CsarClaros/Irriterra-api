<?php

namespace App\Services\Inventario;

use App\Models\Inventario\StockSucursal;
use App\Repositories\Inventario\StockSucursalRepository;
use Illuminate\Support\Facades\DB;

class StockSucursalService
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly StockSucursalRepository $repository
    ) {
    }

    /**
     * Lista registros de stock activos.
     */
    public function index()
    {
        return $this->repository->getAll();
    }

    /**
     * Muestra un registro de stock.
     */
    public function show(int $id): StockSucursal
    {
        return $this->repository->findById($id);
    }

    /**
     * Registra stock por sucursal.
     */
    public function store(array $data): StockSucursal
    {
        return DB::transaction(function () use ($data) {

            $data['stock_actual'] =
                $data['stock_actual'] ?? 0;

            $data['stock_minimo'] =
                $data['stock_minimo'] ?? 0;

            $data['estado_registro'] = 'A';

            return $this->repository->create($data);

        });
    }

    /**
     * Actualiza stock por sucursal.
     */
    public function update(
        StockSucursal $stockSucursal,
        array $data
    ): StockSucursal {

        return DB::transaction(
            function () use ($stockSucursal, $data) {

                return $this->repository->update(

                    $stockSucursal,

                    $data

                );

            }
        );
    }

    /**
     * Eliminación lógica.
     */
    public function destroy(
        StockSucursal $stockSucursal
    ): bool {

        return DB::transaction(
            function () use ($stockSucursal) {

                return $this->repository->delete(

                    $stockSucursal

                );

            }
        );
    }
}