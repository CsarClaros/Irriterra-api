<?php

namespace App\Http\Middleware;

use App\Models\Seguridad\Usuario;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarPermiso
{
    /**
     * Verifica que el usuario autenticado
     * posea el permiso solicitado.
     */
    public function handle(
        Request $request,
        Closure $next,
        string $permiso
    ): Response {

        /** @var Usuario|null $usuario */
        $usuario =
            $request->user();

        if (! $usuario) {

            return response()->json([

                'message' =>
                    'No autenticado.'

            ], 401);

        }

        if (
            $usuario->estado_registro
            !== 'A'
        ) {

            return response()->json([

                'message' =>
                    'La cuenta se encuentra inactiva.'

            ], 403);

        }

        if (
            ! $usuario->tienePermiso(
                $permiso
            )
        ) {

            return response()->json([

                'message' =>
                    'No tiene permiso para realizar esta acción.',

                'permiso_requerido' =>
                    $permiso

            ], 403);

        }

        return $next(
            $request
        );
    }
}
