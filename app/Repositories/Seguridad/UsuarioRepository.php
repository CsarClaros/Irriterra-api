<?php

namespace App\Repositories\Seguridad;

use App\Models\Seguridad\Rol;
use App\Models\Seguridad\Usuario;

class UsuarioRepository
{
    /**
     * Lista todos los usuarios.
     */
    public function getAll(
        bool $incluirSuperAdministrador
    )
    {

        $query =
            Usuario::with([
                'rol',
                'sucursal'
            ]);


        if (
            !$incluirSuperAdministrador
        ) {

            $query->whereHas(
                'rol',
                function ($query) {

                    $query->where(
                        'nombre',
                        '!=',
                        Rol::SUPER_ADMINISTRADOR
                    );

                }
            );

        }


        return $query
            ->orderBy(
                'estado_registro'
            )
            ->orderBy(
                'nombre'
            )
            ->get();
    }


    /**
     * Busca por ID.
     */
    public function findById(
        int $id
    ): Usuario
    {

        return Usuario::with([
            'rol',
            'sucursal'
        ])
            ->findOrFail(
                $id
            );
    }

    /**
     * Busca un usuario por su nombre de acceso.
     *
     * No filtra por estado porque el servicio de
     * autenticación debe poder detectar expresamente
     * una cuenta inactiva.
     */
    public function findByUsuario(
        string $usuario
    ): ?Usuario
    {

        return Usuario::with([
            'rol.permisos',
            'sucursal'
        ])
            ->where(
                'usuario',
                $usuario
            )
            ->first();
    }


    /**
     * Actualiza campos internos de seguridad.
     */
    public function updateSecurity(
        Usuario $usuario,
        array   $data
    ): Usuario
    {

        $usuario->update(
            $data
        );


        return $usuario->fresh([
            'rol.permisos',
            'sucursal'
        ]);
    }


    /**
     * Registra.
     */
    public function create(
        array $data
    ): Usuario
    {

        $usuario =
            Usuario::create(
                $data
            );


        return $usuario->fresh([
            'rol',
            'sucursal'
        ]);
    }


    /**
     * Actualiza.
     */
    public function update(
        Usuario $usuario,
        array   $data
    ): Usuario
    {

        $usuario->update(
            $data
        );


        return $usuario->fresh([
            'rol',
            'sucursal'
        ]);
    }


    /**
     * Eliminación lógica.
     */
    public function delete(
        Usuario $usuario
    ): bool
    {

        return $usuario->update([
            'estado_registro' =>
                'I'
        ]);
    }


    /**
     * Reactiva un usuario.
     */
    public function reactivate(
        Usuario $usuario
    ): Usuario
    {

        $usuario->update([
            'estado_registro' =>
                'A'
        ]);


        return $usuario->fresh([
            'rol',
            'sucursal'
        ]);
    }


    /**
     * Obtiene el ID de un rol activo.
     */
    public function getActiveRoleIdByName(
        string $nombre
    ): ?int
    {

        $id =
            Rol::where(
                'nombre',
                $nombre
            )
                ->where(
                    'estado_registro',
                    'A'
                )
                ->value(
                    'id_rol'
                );


        return $id !== null
            ? (int)$id
            : null;
    }


    /**
     * Cuenta usuarios activos de un rol
     * bloqueando las filas durante la transacción.
     */
    public function countActiveByRoleIdForUpdate(
        int $idRol
    ): int
    {

        return Usuario::where(
            'id_rol',
            $idRol
        )
            ->where(
                'estado_registro',
                'A'
            )
            ->lockForUpdate()
            ->get([
                'id_usuario'
            ])
            ->count();
    }


    /**
     * Verifica si un usuario ya existe.
     */
    public function existsByUsuario(
        string $usuario
    ): bool
    {

        return Usuario::where(
            'usuario',
            $usuario
        )
            ->exists();
    }

    /**
     * Busca un rol activo.
     */
    public function findActiveRoleById(
        int $idRol
    ): ?Rol
    {

        return Rol::where(
            'id_rol',
            $idRol
        )
            ->where(
                'estado_registro',
                'A'
            )
            ->first();
    }


}
