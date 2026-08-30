<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seguridad\StoreRolRequest;
use App\Http\Requests\Seguridad\UpdateRolRequest;
use App\Http\Resources\Seguridad\RolCollection;
use App\Http\Resources\Seguridad\RolResource;
use App\Models\Seguridad\Rol;
use App\Services\Seguridad\RolService;
use Illuminate\Http\JsonResponse;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;

class RolController extends Controller
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly RolService $service
    )
    {
    }

    /**
     * Lista todos los roles activos.
     */
    public function index(
        Request $request
    ): RolCollection
    {

        /** @var Usuario $actor */
        $actor =
            $request->user();


        return new RolCollection(

            $this->service
                ->index(
                    $actor
                )

        );
    }

    /**
     * Registra un nuevo rol.
     */
    public function store(StoreRolRequest $request): JsonResponse
    {
        $rol = $this->service->store(

            $request->validated()

        );

        return response()->json(

            new RolResource($rol),

            201

        );
    }

    /**
     * Muestra un rol.
     */
    public function show(Rol $rol): RolResource
    {
        return new RolResource(

            $this->service->show($rol->id_rol)

        );
    }

    /**
     * Actualiza un rol.
     */
    public function update(
        UpdateRolRequest $request,
        Rol              $rol
    ): JsonResponse
    {

        $rol = $this->service->update(

            $rol,

            $request->validated()

        );

        return response()->json(

            new RolResource($rol),

            200

        );
    }

    /**
     * Eliminación lógica.
     */
    public function destroy(Rol $rol): JsonResponse
    {
        $this->service->destroy($rol);

        return response()->json([

            'message' => 'Rol eliminado correctamente.'

        ], 200);
    }
}
