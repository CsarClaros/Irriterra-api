<?php

namespace App\Services\Seguridad;

use App\Models\Seguridad\Permiso;
use App\Repositories\Seguridad\PermisoRepository;
use Illuminate\Support\Facades\DB;

class PermisoService
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly PermisoRepository $repository
    ) {
    }

    /**
     * Lista permisos.
     */
    public function index()
    {
        return $this->repository->getAll();
    }

    /**
     * Obtiene un permiso.
     */
    public function show(int $id): Permiso
    {
        return $this->repository->findById($id);
    }

    /**
     * Registra un permiso.
     */
    public function store(array $data): Permiso
    {
        return DB::transaction(function () use ($data) {

            $data['estado_registro'] = 'A';

            return $this->repository->create($data);

        });
    }

    /**
     * Actualiza un permiso.
     */
    public function update(Permiso $permiso, array $data): Permiso
    {
        return DB::transaction(function () use ($permiso, $data) {

            return $this->repository->update($permiso, $data);

        });
    }

    /**
     * Eliminación lógica.
     */
    public function destroy(Permiso $permiso): bool
    {
        return DB::transaction(function () use ($permiso) {

            return $this->repository->delete($permiso);

        });
    }
}