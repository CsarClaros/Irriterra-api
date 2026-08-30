<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seguridad\StoreRolPermisoRequest;
use App\Http\Requests\Seguridad\UpdateRolPermisoRequest;
use App\Http\Resources\Seguridad\RolPermisoCollection;
use App\Http\Resources\Seguridad\RolPermisoResource;
use App\Models\Seguridad\RolPermiso;
use App\Services\Seguridad\RolPermisoService;
use Illuminate\Http\JsonResponse;

class RolPermisoController extends Controller
{
    public function __construct(
        private readonly RolPermisoService $service
    ) {
    }

    /**
     * Lista.
     */
    public function index(): RolPermisoCollection
    {
        return new RolPermisoCollection(

            $this->service->index()

        );
    }

    /**
     * Registra.
     */
    public function store(
        StoreRolPermisoRequest $request
    ): JsonResponse {

        $registro = $this->service->store(

            $request->validated()

        );

        return response()->json(

            new RolPermisoResource($registro),

            201

        );
    }

    /**
     * Muestra.
     */
    public function show(
        RolPermiso $rolPermiso
    ): RolPermisoResource {

        return new RolPermisoResource(

            $this->service->show(

                $rolPermiso->id_rol_permiso

            )

        );
    }

    /**
     * Actualiza.
     */
    public function update(

        UpdateRolPermisoRequest $request,

        RolPermiso $rolPermiso

    ): JsonResponse {

        $registro = $this->service->update(

            $rolPermiso,

            $request->validated()

        );

        return response()->json(

            new RolPermisoResource(

                $registro

            ),

            200

        );
    }

    /**
     * Eliminación lógica.
     */
    public function destroy(
        RolPermiso $rolPermiso
    ): JsonResponse {

        $this->service->destroy(

            $rolPermiso

        );

        return response()->json([

            'message' => 'Relación Rol-Permiso eliminada correctamente.'

        ],200);
    }
}