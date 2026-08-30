<?php

namespace App\Http\Controllers\Organizacion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organizacion\StoreSucursalRequest;
use App\Http\Requests\Organizacion\UpdateSucursalRequest;
use App\Http\Resources\Organizacion\SucursalResource;
use App\Models\Organizacion\Sucursal;
use App\Services\Organizacion\SucursalService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class SucursalController extends Controller
{
    /**
     * Servicio del módulo.
     */
    protected SucursalService $sucursalService;

    /**
     * Constructor.
     */
    public function __construct(
        SucursalService $sucursalService
    ) {
        $this->sucursalService = $sucursalService;
    }

    /**
     * Listado de sucursales.
     */
    public function index(): AnonymousResourceCollection
    {
        return SucursalResource::collection(
            $this->sucursalService->all()
        );
    }

    /**
     * Registrar sucursal.
     */
    public function store(
        StoreSucursalRequest $request
    ): SucursalResource {

        $sucursal = $this->sucursalService->create(
            $request->validated()
        );

        return new SucursalResource($sucursal);
    }

    /**
     * Mostrar sucursal.
     */
    public function show(
        Sucursal $sucursal
    ): SucursalResource {

        return new SucursalResource($sucursal);
    }

    /**
     * Actualizar sucursal.
     */
    public function update(
        UpdateSucursalRequest $request,
        Sucursal $sucursal
    ): SucursalResource {

        $sucursal = $this->sucursalService->update(
            $sucursal,
            $request->validated()
        );

        return new SucursalResource($sucursal);
    }

    /**
     * Eliminación lógica.
     */
    public function destroy(
        Sucursal $sucursal
    ): Response {

        $this->sucursalService->delete($sucursal);

        return response()->noContent();
    }
}