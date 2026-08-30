<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventario\StoreCategoriaRequest;
use App\Http\Requests\Inventario\UpdateCategoriaRequest;
use App\Http\Resources\Inventario\CategoriaCollection;
use App\Http\Resources\Inventario\CategoriaResource;
use App\Models\Inventario\Categoria;
use App\Services\Inventario\CategoriaService;
use Illuminate\Http\JsonResponse;

class CategoriaController extends Controller
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly CategoriaService $service
    ) {
    }

    /**
     * Lista categorías activas.
     */
    public function index(): CategoriaCollection
    {
        return new CategoriaCollection(

            $this->service->index()

        );
    }

    /**
     * Registra una categoría.
     */
    public function store(StoreCategoriaRequest $request): JsonResponse
    {
        $categoria = $this->service->store(

            $request->validated()

        );

        return response()->json(

            new CategoriaResource($categoria),

            201

        );
    }

    /**
     * Muestra una categoría.
     */
    public function show(Categoria $categoria): CategoriaResource
    {
        return new CategoriaResource(

            $this->service->show($categoria->id_categoria)

        );
    }

    /**
     * Actualiza una categoría.
     */
    public function update(
        UpdateCategoriaRequest $request,
        Categoria $categoria
    ): JsonResponse {

        $categoria = $this->service->update(

            $categoria,

            $request->validated()

        );

        return response()->json(

            new CategoriaResource($categoria),

            200

        );
    }

    /**
     * Eliminación lógica.
     */
    public function destroy(Categoria $categoria): JsonResponse
    {
        $this->service->destroy($categoria);

        return response()->json([

            'message' => 'Categoría eliminada correctamente.'

        ], 200);
    }
}