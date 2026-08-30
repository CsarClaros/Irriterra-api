<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventario\StorePrecioProductoVarianteRequest;
use App\Http\Requests\Inventario\UpdatePrecioProductoVarianteRequest;
use App\Http\Resources\Inventario\PrecioProductoVarianteCollection;
use App\Http\Resources\Inventario\PrecioProductoVarianteResource;
use App\Models\Inventario\PrecioProductoVariante;
use App\Services\Inventario\PrecioProductoVarianteService;
use Illuminate\Http\JsonResponse;

class PrecioProductoVarianteController extends Controller
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly PrecioProductoVarianteService $service
    ) {
    }

    /**
     * Lista configuraciones de precios activas.
     */
    public function index(): PrecioProductoVarianteCollection
    {
        return new PrecioProductoVarianteCollection(

            $this->service->index()

        );
    }

    /**
     * Registra precios para una variante.
     */
    public function store(
        StorePrecioProductoVarianteRequest $request
    ): JsonResponse {

        $precioProductoVariante =
            $this->service->store(

                $request->validated()

            );

        return response()->json(

            new PrecioProductoVarianteResource(

                $precioProductoVariante

            ),

            201

        );
    }

    /**
     * Muestra una configuración de precios.
     */
    public function show(
        PrecioProductoVariante $precioProductoVariante
    ): PrecioProductoVarianteResource {

        return new PrecioProductoVarianteResource(

            $this->service->show(

                $precioProductoVariante
                    ->id_precio_producto_variante

            )

        );
    }

    /**
     * Actualiza precios de una variante.
     */
    public function update(
        UpdatePrecioProductoVarianteRequest $request,
        PrecioProductoVariante $precioProductoVariante
    ): JsonResponse {

        $precioProductoVariante =
            $this->service->update(

                $precioProductoVariante,

                $request->validated()

            );

        return response()->json(

            new PrecioProductoVarianteResource(

                $precioProductoVariante

            ),

            200

        );
    }

    /**
     * Eliminación lógica.
     */
    public function destroy(
        PrecioProductoVariante $precioProductoVariante
    ): JsonResponse {

        $this->service->destroy(

            $precioProductoVariante

        );

        return response()->json([

            'message' =>
                'Precio de producto variante eliminado correctamente.'

        ], 200);
    }
}