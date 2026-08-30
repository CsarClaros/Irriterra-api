<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Http\Requests\Compras\StoreProveedorRequest;
use App\Http\Requests\Compras\UpdateProveedorRequest;
use App\Http\Resources\Compras\ProveedorCollection;
use App\Http\Resources\Compras\ProveedorResource;
use App\Models\Compras\Proveedor;
use App\Services\Compras\ProveedorService;
use Illuminate\Http\JsonResponse;

class ProveedorController extends Controller
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly ProveedorService $service
    ) {
    }

    /**
     * Lista proveedores activos.
     */
    public function index(): ProveedorCollection
    {
        return new ProveedorCollection(

            $this->service->index()

        );
    }

    /**
     * Registra un proveedor.
     */
    public function store(
        StoreProveedorRequest $request
    ): JsonResponse {

        $proveedor =
            $this->service->store(

                $request->validated()

            );

        return response()->json(

            new ProveedorResource(
                $proveedor
            ),

            201

        );
    }

    /**
     * Muestra un proveedor.
     */
    public function show(
        Proveedor $proveedor
    ): ProveedorResource {

        return new ProveedorResource(

            $this->service->show(

                $proveedor->id_proveedor

            )

        );
    }

    /**
     * Actualiza un proveedor.
     */
    public function update(
        UpdateProveedorRequest $request,
        Proveedor $proveedor
    ): JsonResponse {

        $proveedor =
            $this->service->update(

                $proveedor,

                $request->validated()

            );

        return response()->json(

            new ProveedorResource(
                $proveedor
            ),

            200

        );
    }

    /**
     * Elimina lógicamente un proveedor.
     */
    public function destroy(
        Proveedor $proveedor
    ): JsonResponse {

        $this->service->destroy(
            $proveedor
        );

        return response()->json([

            'message' =>
                'Proveedor eliminado correctamente.'

        ], 200);
    }
}