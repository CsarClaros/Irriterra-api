<?php

namespace App\Services\Seguridad;

use App\Models\Seguridad\RolPermiso;
use App\Repositories\Seguridad\RolPermisoRepository;
use Illuminate\Support\Facades\DB;

class RolPermisoService
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly RolPermisoRepository $repository
    ) {
    }

    /**
     * Lista.
     */
    public function index()
    {
        return $this->repository->getAll();
    }

    /**
     * Obtiene una relación.
     */
    public function show(int $id): RolPermiso
    {
        return $this->repository->findById($id);
    }

    /**
     * Registra.
     */
    public function store(array $data): RolPermiso
    {
        return DB::transaction(function () use ($data) {

            $data['estado_registro'] = 'A';

            return $this->repository->create($data);

        });
    }

    /**
     * Actualiza.
     */
    public function update(
        RolPermiso $rolPermiso,
        array $data
    ): RolPermiso {

        return DB::transaction(function () use (
            $rolPermiso,
            $data
        ) {

            return $this->repository->update(

                $rolPermiso,

                $data

            );

        });
    }

    /**
     * Eliminación lógica.
     */
    public function destroy(
        RolPermiso $rolPermiso
    ): bool {

        return DB::transaction(function () use (
            $rolPermiso
        ) {

            return $this->repository->delete(

                $rolPermiso

            );

        });
    }
}