<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seguridad\StoreUsuarioRequest;
use App\Http\Requests\Seguridad\UpdateUsuarioRequest;
use App\Http\Resources\Seguridad\UsuarioCollection;
use App\Http\Resources\Seguridad\UsuarioResource;
use App\Models\Seguridad\Usuario;
use App\Services\Seguridad\UsuarioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class UsuarioController extends Controller
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly UsuarioService $service
    )
    {
    }

    /**
     * Lista de usuarios.
     */
    public function index(
        Request $request
    ): UsuarioCollection
    {

        /** @var Usuario $actor */
        $actor =
            $request->user();


        return new UsuarioCollection(

            $this->service
                ->index(
                    $actor
                )

        );
    }

    /**
     * Registra un usuario.
     */
    public function store(
        StoreUsuarioRequest $request
    ): JsonResponse
    {

        /** @var Usuario $actor */
        $actor =
            $request->user();


        $usuario =
            $this->service
                ->store(
                    $request->validated(),
                    $actor
                );


        return response()->json(
            new UsuarioResource(
                $usuario
            ),
            201
        );
    }

    /**
     * Muestra un usuario.
     */
    public function show(
        Request $request,
        Usuario $usuario
    ): UsuarioResource
    {

        /** @var Usuario $actor */
        $actor =
            $request->user();


        return new UsuarioResource(

            $this->service
                ->show(
                    $usuario->id_usuario,
                    $actor
                )

        );
    }

    /**
     * Actualiza un usuario.
     */
    public function update(
        UpdateUsuarioRequest $request,
        Usuario              $usuario
    ): UsuarioResource
    {

        /** @var Usuario $actor */
        $actor =
            $request->user();


        return new UsuarioResource(

            $this->service
                ->update(
                    $usuario,
                    $request->validated(),
                    $actor
                )

        );
    }

    /*
|--------------------------------------------------------------------------
| Restablecer contraseña
|--------------------------------------------------------------------------
*/

    public function restablecerPassword(
        Usuario $usuario
    ): JsonResponse
    {

        $this
            ->service
            ->restablecerPassword(
                $usuario
            );


        return response()
            ->json([
                'message' =>
                    'La contraseña fue restablecida correctamente.'
            ]);

    }

    /**
     * Eliminación lógica.
     */
    public function destroy(
        Request $request,
        Usuario $usuario
    ): JsonResponse
    {

        /** @var Usuario $actor */
        $actor =
            $request->user();


        $this->service
            ->destroy(
                $usuario,
                $actor
            );


        return response()->json([
            'message' =>
                'Usuario desactivado correctamente.'
        ]);
    }

    /**
     * Reactiva un usuario.
     */
    public function reactivate(
        Request $request,
        Usuario $usuario
    ): UsuarioResource
    {

        /** @var Usuario $actor */
        $actor =
            $request->user();


        return new UsuarioResource(

            $this->service
                ->reactivate(
                    $usuario,
                    $actor
                )

        );
    }
}
