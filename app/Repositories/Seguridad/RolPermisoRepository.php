<?php

namespace App\Repositories\Seguridad;

use App\Models\Seguridad\RolPermiso;

class RolPermisoRepository
{
    /**
     * Lista relaciones activas.
     */
    public function getAll()
    {
        return RolPermiso::with([

                'rol',

                'permiso'

            ])
            ->where('estado_registro', 'A')
            ->orderBy('id_rol')
            ->orderBy('id_permiso')
            ->get();
    }

    /**
     * Busca por ID.
     */
    public function findById(int $id): RolPermiso
    {
        return RolPermiso::with([

                'rol',

                'permiso'

            ])
            ->findOrFail($id);
    }

    /**
     * Registra.
     */
    public function create(array $data): RolPermiso
    {
        return RolPermiso::create($data)

            ->fresh([

                'rol',

                'permiso'
            ]);
    }

    /**
     * Actualiza.
     */
    public function update(
        RolPermiso $rolPermiso,
        array $data
    ): RolPermiso {

        $rolPermiso->update($data);

        return $rolPermiso->fresh([

            'rol',

            'permiso'
        ]);
    }

    /**
     * Eliminación lógica.
     */
    public function delete(
        RolPermiso $rolPermiso
    ): bool {

        return $rolPermiso->update([

            'estado_registro' => 'I'

        ]);
    }
}