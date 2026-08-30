<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seguridad\LoginRequest;
use App\Http\Resources\Seguridad\UsuarioResource;
use App\Models\Seguridad\Usuario;
use App\Services\Seguridad\AutenticacionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Seguridad\CambiarContrasenaRequest;
use App\Http\Requests\Seguridad\ActualizarPerfilRequest;
use App\Http\Requests\Seguridad\ActualizarFotoPerfilRequest;

class AuthController extends Controller
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly AutenticacionService $service
    )
    {
    }

    /**
     * Inicio de sesión.
     */
    public function login(
        LoginRequest $request
    ): JsonResponse
    {

        $resultado =
            $this->service->login(

                $request->validated()

            );

        return response()->json([

            'message' =>
                'Inicio de sesión correcto.',

            'token' =>
                $resultado['token'],

            'token_type' =>
                $resultado['token_type'],

            'usuario' =>
                new UsuarioResource(

                    $resultado['usuario']

                )

        ]);
    }

    /**
     * Usuario autenticado.
     */
    public function me(
        Request $request
    ): UsuarioResource
    {

        /** @var Usuario $usuario */
        $usuario =
            $request->user();

        return new UsuarioResource(

            $this->service->me(
                $usuario
            )

        );
    }

    /**
     * Actualiza el perfil
     * del usuario autenticado.
     */
    public function actualizarPerfil(
        ActualizarPerfilRequest $request
    ): UsuarioResource
    {

        /** @var Usuario $usuario */
        $usuario =
            $request->user();


        return new UsuarioResource(

            $this->service
                ->actualizarPerfil(

                    $usuario,

                    $request->validated()

                )

        );
    }


    /**
     * Actualiza la fotografía
     * del usuario autenticado.
     */
    public function actualizarFotoPerfil(
        ActualizarFotoPerfilRequest $request
    ): UsuarioResource
    {

        /** @var Usuario $usuario */
        $usuario =
            $request->user();


        return new UsuarioResource(

            $this->service
                ->actualizarFotoPerfil(

                    $usuario,

                    $request->file(
                        'foto'
                    )

                )

        );
    }

    /**
     * Cierra la sesión actual.
     */
    public function logout(
        Request $request
    ): JsonResponse
    {

        /** @var Usuario $usuario */
        $usuario =
            $request->user();

        $this->service->logout(
            $usuario
        );

        return response()->json([

            'message' =>
                'Sesión cerrada correctamente.'

        ]);
    }

    /**
     * Cambia la contraseña del usuario autenticado.
     */
    public function cambiarContrasena(
        CambiarContrasenaRequest $request
    ): JsonResponse
    {

        /** @var Usuario $usuario */
        $usuario =
            $request->user();

        $this->service
            ->cambiarContrasena(

                $usuario,

                $request->validated()

            );

        return response()->json([

            'message' =>
                'Contraseña actualizada correctamente. Inicie sesión nuevamente.'

        ]);
    }
}
