<?php

namespace App\Services\Seguridad;

use App\Models\Seguridad\Rol;
use App\Models\Seguridad\Usuario;
use App\Repositories\Seguridad\RolRepository;
use Illuminate\Support\Facades\DB;

class RolService
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly RolRepository $repository
    )
    {
    }

    /**
     * Lista los roles.
     */
    public function index(
        Usuario $actor
    )
    {

        $actor->loadMissing(
            'rol'
        );


        $incluirSuperAdministrador =
            $actor->rol
            &&
            $actor->rol->nombre
            ===
            Rol::SUPER_ADMINISTRADOR;


        return $this->repository
            ->getAll(
                $incluirSuperAdministrador
            );
    }

    /**
     * Obtiene un rol.
     */
    public function show(int $id): Rol
    {
        return $this->repository->findById($id);
    }

    /**
     * Registra un rol.
     */
    public function store(array $data): Rol
    {
        return DB::transaction(function () use ($data) {

            $data['estado_registro'] = 'A';

            return $this->repository->create($data);

        });
    }

    /**
     * Actualiza un rol.
     */
    public function update(Rol $rol, array $data): Rol
    {
        return DB::transaction(function () use ($rol, $data) {

            return $this->repository->update($rol, $data);

        });
    }

    /**
     * Eliminación lógica.
     */
    public function destroy(Rol $rol): bool
    {
        return DB::transaction(function () use ($rol) {

            return $this->repository->delete($rol);

        });
    }
}
