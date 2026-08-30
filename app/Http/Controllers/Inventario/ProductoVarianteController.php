<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventario\StoreProductoVarianteRequest;
use App\Http\Requests\Inventario\UpdateProductoVarianteRequest;
use App\Http\Resources\Inventario\ProductoVarianteCollection;
use App\Http\Resources\Inventario\ProductoVarianteResource;
use App\Models\Inventario\ProductoVariante;
use App\Services\Inventario\ProductoVarianteService;
use Illuminate\Http\JsonResponse;

class ProductoVarianteController extends Controller
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly ProductoVarianteService $service
    ) {
    }

    /**
     * Lista variantes activas.
     */
    public function index(): ProductoVarianteCollection
    {
        return new ProductoVarianteCollection(

            $this->service->index()

        );
    }

    /**
     * Registra una variante.
     */
    public function store(
        StoreProductoVarianteRequest $request
    ): JsonResponse {

        $productoVariante = $this->service->store(

            $request->validated()

        );

        return response()->json(

            new ProductoVarianteResource(
                $productoVariante
            ),

            201

        );
    }

    /**
     * Muestra una variante.
     */
    public function show(
        ProductoVariante $productoVariante
    ): ProductoVarianteResource {

        return new ProductoVarianteResource(

            $this->service->show(
                $productoVariante->id_producto_variante
            )

        );
    }

    /**
     * Actualiza una variante.
     */
    public function update(
        UpdateProductoVarianteRequest $request,
        ProductoVariante $productoVariante
    ): JsonResponse {

        $productoVariante = $this->service->update(

            $productoVariante,

            $request->validated()

        );

        return response()->json(

            new ProductoVarianteResource(
                $productoVariante
            ),

            200

        );
    }

    /**
     * Eliminación lógica.
     */
    public function destroy(
        ProductoVariante $productoVariante
    ): JsonResponse {

        $this->service->destroy($productoVariante);

        return response()->json([

            'message' =>
                'Variante de producto eliminada correctamente.'

        ], 200);
    }
}