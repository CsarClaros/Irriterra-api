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
use Illuminate\Http\Request;


class ProductoController extends Controller
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly ProductoService $service
    )
    {
    }

    /**
     * Lista productos activos.
     */

    /**
     * Lista productos.
     */
    public function index(
        Request $request
    ): ProductoCollection
    {

        $incluirInactivos =
            $request->boolean(
                'incluir_inactivas'
            );


        return new ProductoCollection(

            $this
                ->service
                ->index(
                    $incluirInactivos
                )

        );

    }

    /**
     * Registra un producto.
     */
    public function store(
        StoreProductoRequest $request
    ): JsonResponse
    {
        $producto = $this->service->store(
            $request->validated()
        );


        return (
        new ProductoResource(
            $producto
        )
        )
            ->response()
            ->setStatusCode(
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
        Producto              $producto
    ): JsonResponse
    {

        $producto = $this->service->update(

            $producto,

            $request->validated()

        );

        return (
        new ProductoResource(
            $producto
        )
        )
            ->response()
            ->setStatusCode(
                200
            );
    }

    /**
     * Desactivación lógica.
     */
    public function destroy(
        Producto $producto
    ): JsonResponse
    {

        $this
            ->service
            ->destroy(
                $producto
            );


        return response()->json([

            'message' =>
                'Producto desactivado correctamente.'

        ], 200);

    }

    /**
     * Reactiva un producto.
     */
    public function reactivate(
        Producto $producto
    ): JsonResponse {

        $this
            ->service
            ->reactivate(
                $producto
            );


        return response()->json([

            'message' =>
                'Producto reactivado correctamente.'

        ], 200);

    }
}
