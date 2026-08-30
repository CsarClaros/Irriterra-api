<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventario\StoreProductoImagenRequest;
use App\Http\Requests\Inventario\UpdateProductoImagenRequest;
use App\Http\Resources\Inventario\ProductoImagenCollection;
use App\Http\Resources\Inventario\ProductoImagenResource;
use App\Models\Inventario\ProductoImagen;
use App\Services\Inventario\ProductoImagenService;
use Illuminate\Http\JsonResponse;

class ProductoImagenController extends Controller
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly ProductoImagenService $service
    ) {
    }

    /**
     * Lista imágenes activas.
     */
    public function index(): ProductoImagenCollection
    {
        return new ProductoImagenCollection(

            $this->service->index()

        );
    }

    /**
     * Registra una imagen.
     */
    public function store(
        StoreProductoImagenRequest $request
    ): JsonResponse {

        $productoImagen = $this->service->store(

            $request->validated()

        );

        return response()->json(

            new ProductoImagenResource(
                $productoImagen
            ),

            201

        );
    }

    /**
     * Muestra una imagen.
     */
    public function show(
        ProductoImagen $productoImagen
    ): ProductoImagenResource {

        return new ProductoImagenResource(

            $this->service->show(
                $productoImagen->id_producto_imagen
            )

        );
    }

    /**
     * Actualiza una imagen.
     */
    public function update(
        UpdateProductoImagenRequest $request,
        ProductoImagen $productoImagen
    ): JsonResponse {

        $productoImagen = $this->service->update(

            $productoImagen,

            $request->validated()

        );

        return response()->json(

            new ProductoImagenResource(
                $productoImagen
            ),

            200

        );
    }

    /**
     * Eliminación lógica.
     */
    public function destroy(
        ProductoImagen $productoImagen
    ): JsonResponse {

        $this->service->destroy($productoImagen);

        return response()->json([

            'message' =>
                'Imagen de producto eliminada correctamente.'

        ], 200);
    }
}