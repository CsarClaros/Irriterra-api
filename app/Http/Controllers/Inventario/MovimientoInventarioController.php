<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventario\StoreMovimientoInventarioRequest;
use App\Http\Resources\Inventario\MovimientoInventarioCollection;
use App\Http\Resources\Inventario\MovimientoInventarioResource;
use App\Models\Inventario\MovimientoInventario;
use App\Services\Inventario\MovimientoInventarioService;
use Illuminate\Http\JsonResponse;

use App\Http\Requests\Inventario\UpdateMovimientoInventarioRequest;

class MovimientoInventarioController extends Controller
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly MovimientoInventarioService $service
    ) {
    }

    /**
     * Lista movimientos de inventario.
     */
    public function index(): MovimientoInventarioCollection
    {
        return new MovimientoInventarioCollection(

            $this->service->index()

        );
    }

    /**
     * Registra un movimiento.
     */
    public function store(
        StoreMovimientoInventarioRequest $request
    ): JsonResponse {

        $movimientoInventario =
            $this->service->store(

                $request->validated()

            );

        return response()->json(

            new MovimientoInventarioResource(
                $movimientoInventario
            ),

            201

        );
    }

    /**
     * Muestra un movimiento.
     */
    public function show(
        MovimientoInventario $movimientoInventario
    ): MovimientoInventarioResource {

        return new MovimientoInventarioResource(

            $this->service->show(

                $movimientoInventario
                    ->id_movimiento_inventario

            )

        );
    }

    /**
 * Actualiza información complementaria del movimiento.
 */
public function update(
    UpdateMovimientoInventarioRequest $request,
    MovimientoInventario $movimientoInventario
): JsonResponse {

    $movimientoInventario = $this->service->update(

        $movimientoInventario,

        $request->validated()

    );

    return response()->json(

        new MovimientoInventarioResource(
            $movimientoInventario
        ),

        200

    );
}
}