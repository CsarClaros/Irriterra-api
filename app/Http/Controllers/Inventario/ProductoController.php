<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventario\StoreProductoRequest;
use App\Http\Requests\Inventario\UpdateProductoRequest;
use App\Http\Resources\Inventario\ProductoCollection;
use App\Http\Resources\Inventario\ProductoResource;
use App\Models\Inventario\Producto;
use App\Services\Inventario\ProductoService;
use Illuminate\Http\JsonResponse;

class ProductoController extends Controller
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly ProductoService $service
    ) {
    }

    /**
     * Lista productos activos.
     */
    public function index(): ProductoCollection
    {
        return new ProductoCollection(

            $this->service->index()

        );
    }

    /**
     * Registra un producto.
     */
    public function store(StoreProductoRequest $request): JsonResponse
    {
        $producto = $this->service->store(

            $request->validated()

        );

        return response()->json(

            new ProductoResource($producto),

            201

        );
    }

    /**
     * Muestra un producto.
     */
    public function show(Producto $producto): ProductoResource
    {
        return new ProductoResource(

            $this->service->show($producto->id_producto)

        );
    }

    /**
     * Actualiza un producto.
     */
    public function update(
        UpdateProductoRequest $request,
        Producto $producto
    ): JsonResponse {

        $producto = $this->service->update(

            $producto,

            $request->validated()

        );

        return response()->json(

            new ProductoResource($producto),

            200

        );
    }

    /**
     * Eliminación lógica.
     */
    public function destroy(Producto $producto): JsonResponse
    {
        $this->service->destroy($producto);

        return response()->json([

            'message' => 'Producto eliminado correctamente.'

        ], 200);
    }
}