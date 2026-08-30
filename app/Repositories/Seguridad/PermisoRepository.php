<?php

namespace App\Repositories\Seguridad;

use App\Models\Seguridad\Permiso;

class PermisoRepository
{
    /**
     * Lista permisos activos.
     */
    public function getAll()
    {
        return Permiso::where('estado_registro', 'A')
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Busca por ID.
     */
    public function findById(int $id): Permiso
    {
        return Permiso::findOrFail($id);
    }

    /**
     * Registra un permiso.
     */
    public function create(array $data): Permiso
    {
        return Permiso::create($data);
    }

    /**
     * Actualiza un permiso.
     */
    public function update(Permiso $permiso, array $data): Permiso
    {
        $permiso->update($data);

        return $permiso->fresh();
    }

    /**
     * Eliminación lógica.
     */
    public function delete(Permiso $permiso): bool
    {
        return $permiso->update([

            'estado_registro' => 'I'

        ]);
    }
}