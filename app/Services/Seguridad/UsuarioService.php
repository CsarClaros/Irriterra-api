<?php

namespace App\Services\Seguridad;

use App\Models\Seguridad\Usuario;
use App\Repositories\Seguridad\UsuarioRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Seguridad\Rol;


class UsuarioService
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly UsuarioRepository $repository
    )
    {
    }

    /**
     * Lista.
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
     * Obtiene un usuario.
     */
    public function show(
        int     $id,
        Usuario $actor
    ): Usuario
    {

        $usuario =
            $this->repository
                ->findById(
                    $id
                );


        $actor->loadMissing(
            'rol'
        );


        $usuario->loadMissing(
            'rol'
        );


        if (
            $usuario->rol
            &&
            $usuario->rol->nombre
            === Rol::SUPER_ADMINISTRADOR
            &&
            (
                !$actor->rol
                ||
                $actor->rol->nombre
                !== Rol::SUPER_ADMINISTRADOR
            )
        ) {

            abort(
                404
            );

        }


        return $usuario;
    }

    /**
     * Registra.
     */

    public function store(
        array   $data,
        Usuario $actor
    ): Usuario
    {
        $this->validarAsignacionRol(
            $actor,
            (int)$data['id_rol']
        );

        $rutaFoto = null;


        $foto =
            $data['foto']
            ?? null;


        unset(
            $data['foto']
        );


        if (
            $foto instanceof UploadedFile
        ) {

            $rutaFoto =
                $foto->store(
                    'usuarios/perfil',
                    'public'
                );


            $data['foto'] =
                $rutaFoto;

        }


        try {

            return DB::transaction(
                function () use (
                    $data
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Usuario
                    |--------------------------------------------------------------------------
                    */

                    $data['usuario'] =
                        $this->generarUsuario(

                            $data['nombre'],

                            $data['apellido_paterno'],

                            $data['apellido_materno']
                            ?? null

                        );


                    /*
                    |--------------------------------------------------------------------------
                    | Contraseña inicial
                    |--------------------------------------------------------------------------
                    */

                    $data['password'] =
                        Hash::make(
                            $data['ci']
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | Estado
                    |--------------------------------------------------------------------------
                    */

                    $data['estado_registro'] =
                        'A';


                    return $this->repository
                        ->create(
                            $data
                        );

                }
            );

        } catch (
        Throwable $exception
        ) {

            if (
                $rutaFoto
            ) {

                Storage::disk(
                    'public'
                )
                    ->delete(
                        $rutaFoto
                    );

            }


            throw $exception;

        }

    }

    /**
     * Actualiza.
     */
    public function update(
        Usuario $usuario,
        array   $data,
        Usuario $actor
    ): Usuario
    {
        $this->validarGestionUsuario(
            $actor,
            $usuario
        );


        $this->validarAsignacionRol(
            $actor,
            (int)$data['id_rol']
        );

        $fotoAnterior =
            $usuario->foto;


        $rutaFotoNueva =
            null;


        $foto =
            $data['foto']
            ?? null;


        if (
            $foto instanceof UploadedFile
        ) {

            $rutaFotoNueva =
                $foto->store(
                    'usuarios/perfil',
                    'public'
                );


            $data['foto'] =
                $rutaFotoNueva;

        } else {

            /*
             * Si no seleccionó una fotografía nueva,
             * conserva la fotografía anterior.
             */

            unset(
                $data['foto']
            );

        }

        $this->validarCambioRolCritico(
            $usuario,
            $data
        );

        try {

            $usuarioActualizado =
                DB::transaction(
                    function () use (
                        $usuario,
                        $data
                    ) {
                        $this->validarCambioRolCritico(
                            $usuario,
                            $data
                        );

//                        $this->validarCambioRolCritico(
//                            $usuario,
//                            $data
//                        );

                        return $this->repository
                            ->update(
                                $usuario,
                                $data
                            );

                    }
                );

        } catch (
        Throwable $exception
        ) {

            if (
                $rutaFotoNueva
            ) {

                Storage::disk(
                    'public'
                )
                    ->delete(
                        $rutaFotoNueva
                    );

            }


            throw $exception;

        }


        /*
         * Solo después de actualizar correctamente
         * la BD eliminamos la fotografía anterior.
         */

        if (
            $rutaFotoNueva
            &&
            $fotoAnterior
        ) {

            Storage::disk(
                'public'
            )
                ->delete(
                    $fotoAnterior
                );

        }


        return $usuarioActualizado;

    }

    /**
     * Eliminación lógica.
     */
    public function destroy(
        Usuario $usuario,
        Usuario $actor
    ): bool
    {

        return DB::transaction(
            function () use (
                $usuario,
                $actor
            ) {

                /*
                 * Nadie puede desactivarse
                 * desde administración.
                 */
                if (
                    $usuario->id_usuario
                    ===
                    $actor->id_usuario
                ) {

                    $this->lanzarErrorNegocio(
                        'No puede desactivar su propia cuenta.'
                    );
                }


                $this->validarGestionUsuario(
                    $actor,
                    $usuario
                );


                $this->validarDesactivacionRolCritico(
                    $usuario
                );


                return $this->repository
                    ->delete(
                        $usuario
                    );

            }
        );
    }

    /**
     * Reactiva un usuario.
     */
    public function reactivate(
        Usuario $usuario,
        Usuario $actor
    ): Usuario
    {

        return DB::transaction(
            function () use (
                $usuario,
                $actor
            ) {

                $this->validarGestionUsuario(
                    $actor,
                    $usuario
                );


                if (
                    $usuario->estado_registro
                    === 'A'
                ) {

                    $this->lanzarErrorNegocio(
                        'El usuario ya se encuentra activo.'
                    );
                }


                return $this->repository
                    ->reactivate(
                        $usuario
                    );

            }
        );
    }


    // Genera automaticamente el nombre de usuario

    /**
     * Genera automáticamente el nombre de usuario.
     *
     * Regla de negocio:
     *
     * - Convierte todos los caracteres a minúsculas.
     * - Elimina acentos y caracteres especiales.
     * - Ignora conectores como:
     *      de, del, la, las, los, da, do, dos, das, y, e.
     * - Si existen dos nombres válidos:
     *      primera letra del primer nombre +
     *      primera letra del segundo nombre.
     * - Si existe un solo nombre:
     *      dos primeras letras.
     * - Utiliza el apellido paterno.
     * - Si no existe apellido paterno,
     *      utiliza el materno.
     * - Si el usuario ya existe,
     *      agrega un número consecutivo.
     *
     * Ejemplos:
     *
     * Cesar Raymundo Claros
     *      -> crclaros
     *
     * Juan Perez
     *      -> juperez
     *
     * Maria del Carmen Lopez
     *      -> mclopez
     *
     * Jose Angel Fernandez
     *      -> jafernandez
     *
     * Ana Choquemisa
     *      -> anchoquemisa
     */
    private function generarUsuario(

        string  $nombre,

        ?string $apellidoPaterno,

        ?string $apellidoMaterno

    ): string
    {

        /*
|--------------------------------------------------------------------------
| Obtiene los nombres válidos
|--------------------------------------------------------------------------
*/

        $nombres = $this->normalizarPalabras($nombre);

        /*
|--------------------------------------------------------------------------
| Prefijo
|--------------------------------------------------------------------------
*/

        if (count($nombres) >= 2) {

            $prefijo =
                substr($nombres[0], 0, 1) .
                substr($nombres[1], 0, 1);
        } else {

            $prefijo =
                substr($nombres[0], 0, 2);
        }

        /*
|--------------------------------------------------------------------------
| Apellido
|--------------------------------------------------------------------------
*/

        $apellido = $apellidoPaterno;

        if (blank($apellido)) {

            $apellido = $apellidoMaterno;
        }

        $apellido = implode(

            '',

            $this->normalizarPalabras($apellido)

        );

        /*
|--------------------------------------------------------------------------
| Usuario base
|--------------------------------------------------------------------------
*/

        $usuario = strtolower($prefijo . $apellido);

        /*
|--------------------------------------------------------------------------
| Evita duplicados
|--------------------------------------------------------------------------
*/

        $original = $usuario;

        $contador = 1;

        while (

        $this->repository
            ->existsByUsuario($usuario)

        ) {

            $usuario =

                $original . $contador;

            $contador++;
        }

        return $usuario;
    }

    /**
     * Normaliza nombres y apellidos.
     *
     * Elimina:
     *
     * - acentos
     * - Ñ
     * - caracteres especiales
     * - conectores
     * - espacios repetidos
     */
    private function normalizarPalabras(?string $texto): array
    {

        if (blank($texto)) {

            return [];
        }

        /*
|--------------------------------------------------------------------------
| Elimina acentos
|--------------------------------------------------------------------------
*/

        $texto = iconv(

            'UTF-8',

            'ASCII//TRANSLIT',

            $texto

        );

        /*
|--------------------------------------------------------------------------
| Minúsculas
|--------------------------------------------------------------------------
*/

        $texto = strtolower($texto);

        /*
|--------------------------------------------------------------------------
| Solo letras y espacios
|--------------------------------------------------------------------------
*/

        $texto = preg_replace(

            '/[^a-z\s]/',

            '',

            $texto

        );

        /*
|--------------------------------------------------------------------------
| Divide palabras
|--------------------------------------------------------------------------
*/

        $palabras = preg_split(

            '/\s+/',

            trim($texto)

        );

        /*
|--------------------------------------------------------------------------
| Conectores ignorados
|--------------------------------------------------------------------------
*/

        $ignorar = [

            'de',

            'del',

            'la',

            'las',

            'los',

            'da',

            'do',

            'dos',

            'das',

            'y',

            'e'

        ];

        return array_values(

            array_filter(

                $palabras,

                fn($palabra) => !in_array(

                    $palabra,

                    $ignorar

                )

            )

        );
    }

    /**
     * Evita cambiar el rol del último
     * Administrador activo.
     */
    private function validarCambioRolCritico(
        Usuario $usuario,
        array   $data
    ): void
    {

        if (
            $usuario->estado_registro
            !== 'A'
            ||
            !array_key_exists(
                'id_rol',
                $data
            )
        ) {

            return;
        }


        $usuario->loadMissing(
            'rol'
        );


        if (
            !$usuario->rol
        ) {

            return;
        }


        if (
            !in_array(
                $usuario->rol->nombre,
                [
                    Rol::SUPER_ADMINISTRADOR,
                    Rol::ADMINISTRADOR
                ],
                true
            )
        ) {

            return;
        }


        if (
            (int)$data['id_rol']
            ===
            (int)$usuario->id_rol
        ) {

            return;
        }


        $cantidad =
            $this->repository
                ->countActiveByRoleIdForUpdate(
                    $usuario->id_rol
                );


        if (
            $cantidad <= 1
        ) {

            $this->lanzarErrorNegocio(
                'No puede cambiar el rol del último '
                . $usuario->rol->nombre
                . ' activo.'
            );
        }
    }


    /**
     * Evita desactivar al último
     * Administrador activo.
     */
    private function validarDesactivacionRolCritico(
        Usuario $usuario
    ): void
    {

        if (
            $usuario->estado_registro
            !== 'A'
        ) {

            return;
        }


        $usuario->loadMissing(
            'rol'
        );


        if (
            !$usuario->rol
            ||
            !in_array(
                $usuario->rol->nombre,
                [
                    Rol::SUPER_ADMINISTRADOR,
                    Rol::ADMINISTRADOR
                ],
                true
            )
        ) {

            return;
        }


        $cantidad =
            $this->repository
                ->countActiveByRoleIdForUpdate(
                    $usuario->id_rol
                );


        if (
            $cantidad <= 1
        ) {

            $this->lanzarErrorNegocio(
                'No puede desactivar al último '
                . $usuario->rol->nombre
                . ' activo.'
            );
        }
    }


    /**
     * Error de regla de negocio.
     */
    private function lanzarErrorNegocio(
        string $mensaje
    ): never
    {

        throw new HttpResponseException(
            response()->json(
                [
                    'message' =>
                        $mensaje
                ],
                422
            )
        );
    }

    /**
     * Comprueba si el actor puede gestionar
     * al usuario objetivo.
     */
    private function validarGestionUsuario(
        Usuario $actor,
        Usuario $objetivo
    ): void
    {

        $actor->loadMissing(
            'rol'
        );


        $objetivo->loadMissing(
            'rol'
        );


        if (
            !$actor->rol
            ||
            !$objetivo->rol
        ) {

            $this->lanzarErrorNegocio(
                'No fue posible determinar la jerarquía de los usuarios.'
            );
        }


        $permitido =
            match (
            $actor->rol->nombre
            ) {

                Rol::SUPER_ADMINISTRADOR =>
                    $objetivo->rol->nivel
                    <=
                    $actor->rol->nivel,

                Rol::ADMINISTRADOR =>
                    $objetivo->rol->nivel
                    <=
                    $actor->rol->nivel,

                Rol::GERENTE =>
                    $objetivo->rol->nivel
                    <
                    $actor->rol->nivel,

                default =>
                false

            };


        if (
            !$permitido
        ) {

            $this->lanzarErrorNegocio(
                'No tiene autorización para administrar a este usuario.'
            );
        }
    }

    private function validarAsignacionRol(
        Usuario $actor,
        int     $idRol
    ): void
    {

        $actor->loadMissing(
            'rol'
        );


        if (
            !$actor->rol
        ) {

            $this->lanzarErrorNegocio(
                'No fue posible determinar el rol del usuario autenticado.'
            );
        }


        $rolDestino =
            $this->repository
                ->findActiveRoleById(
                    $idRol
                );


        if (
            !$rolDestino
        ) {

            $this->lanzarErrorNegocio(
                'El rol seleccionado no se encuentra activo.'
            );
        }


        $permitido =
            match (
            $actor->rol->nombre
            ) {

                Rol::SUPER_ADMINISTRADOR =>
                    $rolDestino->nivel
                    <=
                    $actor->rol->nivel,

                Rol::ADMINISTRADOR =>
                    $rolDestino->nivel
                    <=
                    $actor->rol->nivel,

                Rol::GERENTE =>
                    $rolDestino->nivel
                    <
                    $actor->rol->nivel,

                default =>
                false

            };


        if (
            !$permitido
        ) {

            $this->lanzarErrorNegocio(
                'No tiene autorización para asignar el rol seleccionado.'
            );
        }
    }
}
