<?php

namespace App\Services\Compras;

use App\Models\Compras\Proveedor;
use App\Repositories\Compras\ProveedorRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProveedorService
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly ProveedorRepository $repository
    ) {
    }

    /**
     * Lista proveedores activos.
     */
    public function index(): Collection
    {
        return $this->repository->getAll();
    }

    /**
     * Muestra un proveedor activo.
     */
    public function show(
        int $id
    ): Proveedor {

        return $this->repository->findById(
            $id
        );
    }

    /**
     * Registra un proveedor.
     */
    public function store(
        array $data
    ): Proveedor {

        return DB::transaction(
            function () use ($data) {

                $data['tipo_proveedor'] =
                    $data['tipo_proveedor']
                    ?? Proveedor::EMPRESA;

                $data['estado_registro'] = 'A';

                return $this->repository->create(
                    $data
                );

            }
        );
    }

    /**
     * Actualiza un proveedor.
     */
    public function update(
        Proveedor $proveedor,
        array $data
    ): Proveedor {

        return DB::transaction(
            function () use (
                $proveedor,
                $data
            ) {

                $proveedor =
                    $this->repository
                        ->findByIdForUpdate(

                            $proveedor->id_proveedor

                        );

                unset(
                    $data['estado_registro'],
                    $data['usuario_creacion']
                );

                return $this->repository->update(

                    $proveedor,

                    $data

                );

            }
        );
    }

    /**
     * Elimina lógicamente un proveedor.
     */
    public function destroy(
        Proveedor $proveedor
    ): bool {

        return DB::transaction(
            function () use ($proveedor) {

                $proveedor =
                    $this->repository
                        ->findByIdForUpdate(

                            $proveedor->id_proveedor

                        );

                return $this->repository->delete(
                    $proveedor
                );

            }
        );
    }
}