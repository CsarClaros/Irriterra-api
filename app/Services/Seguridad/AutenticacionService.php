<?php

namespace App\Services\Seguridad;

use App\Models\Seguridad\Usuario;
use App\Repositories\Seguridad\UsuarioRepository;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AutenticacionService
{
    /**
     * Máximo de intentos fallidos.
     */
    private const MAX_INTENTOS = 5;

    /**
     * Minutos de bloqueo.
     */
    private const MINUTOS_BLOQUEO = 15;

    /**
     * Constructor.
     */
    public function __construct(
        private readonly UsuarioRepository $repository
    )
    {
    }

    /**
     * Inicia sesión.
     */
    public function login(
        array $data
    ): array
    {

        $usuario =
            $this->repository
                ->findByUsuario(

                    $data['usuario']

                );

        /*
        |--------------------------------------------------------------------------
        | Usuario inexistente
        |--------------------------------------------------------------------------
        */

        if (!$usuario) {

            $this->credencialesInvalidas();

        }

        /*
        |--------------------------------------------------------------------------
        | Usuario inactivo
        |--------------------------------------------------------------------------
        */

        if (
            $usuario->estado_registro
            !== 'A'
        ) {

            throw new HttpResponseException(

                response()->json([

                    'message' =>
                        'La cuenta se encuentra inactiva.'

                ], 403)

            );

        }

        /*
        |--------------------------------------------------------------------------
        | Comprueba bloqueo activo
        |--------------------------------------------------------------------------
        */

        if (
            $usuario->bloqueado_hasta
            && now()->lessThan(
                $usuario->bloqueado_hasta
            )
        ) {

            throw new HttpResponseException(

                response()->json([

                    'message' =>
                        'La cuenta está temporalmente bloqueada por múltiples intentos fallidos.',

                    'bloqueado_hasta' =>
                        $usuario->bloqueado_hasta

                ], 423)

            );

        }

        /*
        |--------------------------------------------------------------------------
        | Limpia un bloqueo vencido
        |--------------------------------------------------------------------------
        */

        if (
            $usuario->bloqueado_hasta
            && now()->greaterThanOrEqualTo(
                $usuario->bloqueado_hasta
            )
        ) {

            $usuario =
                $this->repository
                    ->updateSecurity(

                        $usuario,

                        [
                            'intentos_fallidos' =>
                                0,

                            'bloqueado_hasta' =>
                                null
                        ]

                    );

        }

        /*
        |--------------------------------------------------------------------------
        | Verifica contraseña
        |--------------------------------------------------------------------------
        */

        if (
            !Hash::check(
                $data['contrasena'],
                $usuario->password
            )
        ) {

            $this->registrarIntentoFallido(
                $usuario
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Inicio exitoso
        |--------------------------------------------------------------------------
        */

        $usuario =
            $this->repository
                ->updateSecurity(

                    $usuario,

                    [
                        'ultimo_acceso' =>
                            now(),

                        'intentos_fallidos' =>
                            0,

                        'bloqueado_hasta' =>
                            null
                    ]

                );

        /*
        |--------------------------------------------------------------------------
        | Token
        |--------------------------------------------------------------------------
        */

        $token =
            $usuario
                ->createToken(
                    'irriterra-web'
                )
                ->plainTextToken;

        return [

            'token' =>
                $token,

            'token_type' =>
                'Bearer',

            'usuario' =>
                $usuario

        ];
    }

    /**
     * Obtiene el usuario autenticado.
     */
    public function me(
        Usuario $usuario
    ): Usuario
    {

        return $usuario->load([

            'rol.permisos',

            'sucursal'

        ]);
    }

    /**
     * Cierra la sesión actual.
     */
    public function logout(
        Usuario $usuario
    ): void
    {

        $token =
            $usuario->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {

            $token->delete();

        }
    }

    /**
     * Registra un intento incorrecto.
     */
    private function registrarIntentoFallido(
        Usuario $usuario
    ): void
    {

        $intentos =
            $usuario->intentos_fallidos + 1;

        $data = [

            'intentos_fallidos' =>
                $intentos

        ];

        if (
            $intentos
            >= self::MAX_INTENTOS
        ) {

            $data['bloqueado_hasta'] =
                now()->addMinutes(
                    self::MINUTOS_BLOQUEO
                );

            $this->repository
                ->updateSecurity(

                    $usuario,

                    $data

                );

            throw new HttpResponseException(

                response()->json([

                    'message' =>
                        'La cuenta fue bloqueada temporalmente por múltiples intentos fallidos.',

                    'bloqueado_hasta' =>
                        $data['bloqueado_hasta']

                ], 423)

            );

        }

        $this->repository
            ->updateSecurity(

                $usuario,

                $data

            );

        $this->credencialesInvalidas();
    }

    /**
     * Respuesta para credenciales incorrectas.
     */
    private function credencialesInvalidas(): void
    {
        throw new HttpResponseException(

            response()->json([

                'message' =>
                    'Usuario o contraseña incorrectos.'

            ], 401)

        );
    }


    /**
     * Actualiza los datos permitidos
     * del perfil autenticado.
     */
    public function actualizarPerfil(
        Usuario $usuario,
        array   $data
    ): Usuario
    {

        return DB::transaction(
            function () use (
                $usuario,
                $data
            ): Usuario {

                $usuario =
                    $this->repository
                        ->update(
                            $usuario,
                            $data
                        );

                return $usuario
                    ->load([
                        'rol.permisos',
                        'sucursal'
                    ]);

            }
        );
    }


    /**
     * Actualiza la fotografía
     * del usuario autenticado.
     */
    public function actualizarFotoPerfil(
        Usuario      $usuario,
        UploadedFile $foto
    ): Usuario
    {

        $fotoAnterior =
            $usuario->foto;


        $rutaNueva =
            $foto->store(
                'usuarios/perfil',
                'public'
            );


        try {

            $usuario =
                $this->repository
                    ->update(
                        $usuario,
                        [
                            'foto' =>
                                $rutaNueva
                        ]
                    );

        } catch (
        Throwable $exception
        ) {

            Storage::disk(
                'public'
            )
                ->delete(
                    $rutaNueva
                );


            throw $exception;

        }


        if (
            $fotoAnterior
        ) {

            Storage::disk(
                'public'
            )
                ->delete(
                    $fotoAnterior
                );

        }


        return $usuario
            ->load([
                'rol.permisos',
                'sucursal'
            ]);

    }

    /**
     * Cambia la contraseña del usuario autenticado.
     *
     * Por seguridad, después del cambio se invalidan
     * todas las sesiones activas.
     */
    public function cambiarContrasena(
        Usuario $usuario,
        array   $data
    ): void
    {

        DB::transaction(
            function () use (
                $usuario,
                $data
            ): void {

                /*
                |--------------------------------------------------------------------------
                | Verifica contraseña actual
                |--------------------------------------------------------------------------
                */

                if (
                    !Hash::check(
                        $data['contrasena_actual'],
                        $usuario->password
                    )
                ) {

                    throw new HttpResponseException(

                        response()->json([

                            'message' =>
                                'La contraseña actual es incorrecta.',

                            'errors' => [

                                'contrasena_actual' => [

                                    'La contraseña actual es incorrecta.'

                                ]

                            ]

                        ], 422)

                    );

                }

                /*
                |--------------------------------------------------------------------------
                | Guarda nueva contraseña
                |--------------------------------------------------------------------------
                */

                $this->repository
                    ->updateSecurity(

                        $usuario,

                        [
                            'password' =>
                                Hash::make(
                                    $data['nueva_contrasena']
                                ),

                            'intentos_fallidos' =>
                                0,

                            'bloqueado_hasta' =>
                                null
                        ]

                    );

                /*
                |--------------------------------------------------------------------------
                | Revoca todas las sesiones
                |--------------------------------------------------------------------------
                */

                $usuario
                    ->tokens()
                    ->delete();

            }
        );
    }
}
