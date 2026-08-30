<?php

namespace App\Repositories\Seguridad;

use App\Models\Seguridad\Rol;

class RolRepository
{
    /**
     * Obtiene todos los registros activos.
     */
    public function getAll(
        bool $incluirSuperAdministrador
    ) {

        $query =
            Rol::where(
                'estado_registro',
                'A'
            );


        if (
            ! $incluirSuperAdministrador
        ) {

            $query->where(
                'nombre',
                '!=',
                Rol::SUPER_ADMINISTRADOR
            );

        }


        return $query
            ->orderByDesc(
                'nivel'
            )
            ->orderBy(
                'nombre'
            )
            ->get();
    }

    /**
     * Busca un registro por ID.
     */
    public function findById(int $id): Rol
    {
        return Rol::findOrFail($id);
    }

    /**
     * Registra un nuevo rol.
     */
    public function create(array $data): Rol
    {
        return Rol::create($data);
    }

    /**
     * Actualiza un rol.
     */
    public function update(Rol $rol, array $data): Rol
    {
        $rol->update($data);

        return $rol->fresh();
    }

    /**
     * Eliminación lógica.
     */
    public function delete(Rol $rol): bool
    {
        return $rol->update([
            'estado_registro' => 'I'
        ]);
    }
}
