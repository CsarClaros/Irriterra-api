<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seguridad\StorePermisoRequest;
use App\Http\Requests\Seguridad\UpdatePermisoRequest;
use App\Http\Resources\Seguridad\PermisoCollection;
use App\Http\Resources\Seguridad\PermisoResource;
use App\Models\Seguridad\Permiso;
use App\Services\Seguridad\PermisoService;
use Illuminate\Http\JsonResponse;

class PermisoController extends Controller
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly PermisoService $service
    ) {
    }

    /**
     * Lista permisos activos.
     */
    public function index(): PermisoCollection
    {
        return new PermisoCollection(

            $this->service->index()

        );
    }

    /**
     * Registra un permiso.
     */
    public function store(StorePermisoRequest $request): JsonResponse
    {
        $permiso = $this->service->store(

            $request->validated()

        );

        return response()->json(

            new PermisoResource($permiso),

            201

        );
    }

    /**
     * Muestra un permiso.
     */
    public function show(Permiso $permiso): PermisoResource
    {
        return new PermisoResource(

            $this->service->show($permiso->id_permiso)

        );
    }

    /**
     * Actualiza un permiso.
     */
    public function update(
        UpdatePermisoRequest $request,
        Permiso $permiso
    ): JsonResponse {

        $permiso = $this->service->update(

            $permiso,

            $request->validated()

        );

        return response()->json(

            new PermisoResource($permiso),

            200

        );
    }

    /**
     * Eliminación lógica.
     */
    public function destroy(Permiso $permiso): JsonResponse
    {
        $this->service->destroy($permiso);

        return response()->json([

            'message' => 'Permiso eliminado correctamente.'

        ], 200);
    }
}