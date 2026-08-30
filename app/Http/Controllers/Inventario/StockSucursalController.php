<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventario\StoreStockSucursalRequest;
use App\Http\Requests\Inventario\UpdateStockSucursalRequest;
use App\Http\Resources\Inventario\StockSucursalCollection;
use App\Http\Resources\Inventario\StockSucursalResource;
use App\Models\Inventario\StockSucursal;
use App\Services\Inventario\StockSucursalService;
use Illuminate\Http\JsonResponse;

class StockSucursalController extends Controller
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly StockSucursalService $service
    ) {
    }

    /**
     * Lista registros de stock activos.
     */
    public function index(): StockSucursalCollection
    {
        return new StockSucursalCollection(

            $this->service->index()

        );
    }

    /**
     * Registra stock por sucursal.
     */
    public function store(
        StoreStockSucursalRequest $request
    ): JsonResponse {

        $stockSucursal = $this->service->store(

            $request->validated()

        );

        return response()->json(

            new StockSucursalResource(
                $stockSucursal
            ),

            201

        );
    }

    /**
     * Muestra un registro de stock.
     */
    public function show(
        StockSucursal $stockSucursal
    ): StockSucursalResource {

        return new StockSucursalResource(

            $this->service->show(
                $stockSucursal->id_stock_sucursal
            )

        );
    }

    /**
     * Actualiza stock por sucursal.
     */
    public function update(
        UpdateStockSucursalRequest $request,
        StockSucursal $stockSucursal
    ): JsonResponse {

        $stockSucursal = $this->service->update(

            $stockSucursal,

            $request->validated()

        );

        return response()->json(

            new StockSucursalResource(
                $stockSucursal
            ),

            200

        );
    }

    /**
     * Eliminación lógica.
     */
    public function destroy(
        StockSucursal $stockSucursal
    ): JsonResponse {

        $this->service->destroy($stockSucursal);

        return response()->json([

            'message' =>
                'Registro de stock por sucursal eliminado correctamente.'

        ], 200);
    }
}