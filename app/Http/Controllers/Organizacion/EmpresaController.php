<?php

namespace App\Http\Controllers\Organizacion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organizacion\StoreEmpresaRequest;
use App\Http\Requests\Organizacion\UpdateEmpresaRequest;
use App\Http\Resources\Organizacion\EmpresaResource;
use App\Models\Organizacion\Empresa;
use App\Services\Organizacion\EmpresaService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class EmpresaController extends Controller
{
    /**
     * Servicio del módulo Empresa.
     */
    protected EmpresaService $empresaService;

    /**
     * Constructor.
     */
    public function __construct(EmpresaService $empresaService)
    {
        $this->empresaService = $empresaService;
    }

    /**
     * Listado de empresas.
     */
    public function index(): AnonymousResourceCollection
    {
        return EmpresaResource::collection(
            $this->empresaService->all()
        );
    }

    /**
     * Registrar empresa.
     */
    public function store(StoreEmpresaRequest $request): EmpresaResource
    {
        $empresa = $this->empresaService->create(
            $request->validated()
        );

        return new EmpresaResource($empresa);
    }

    /**
     * Mostrar empresa.
     */
    public function show(Empresa $empresa): EmpresaResource
    {
        return new EmpresaResource($empresa);
    }

    /**
     * Actualizar empresa.
     */
    public function update(
        UpdateEmpresaRequest $request,
        Empresa $empresa
    ): EmpresaResource {

        $empresa = $this->empresaService->update(
            $empresa,
            $request->validated()
        );

        return new EmpresaResource($empresa);
    }

    /**
     * Eliminación lógica.
     */
    public function destroy(Empresa $empresa): Response
    {
        $this->empresaService->delete($empresa);

        return response()->noContent();
    }
}