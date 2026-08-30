<?php

namespace App\Services\Inventario;

use App\Models\Inventario\MovimientoInventario;
use App\Repositories\Inventario\MovimientoInventarioRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MovimientoInventarioService
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly MovimientoInventarioRepository $repository
    ) {
    }

    /**
     * Lista movimientos activos.
     */
    public function index()
    {
        return $this->repository->getAll();
    }

    /**
     * Muestra un movimiento.
     */
    public function show(int $id): MovimientoInventario
    {
        return $this->repository->findById($id);
    }

    /**
     * Registra un movimiento de inventario.
     */
    public function store(
        array $data
    ): MovimientoInventario {

        return DB::transaction(function () use ($data) {

            $stockSucursal =
                $this->repository->findStockForUpdate(

                    $data['id_stock_sucursal']

                );

            $stockAnterior = round(

                (float) $stockSucursal->stock_actual,

                3

            );

            $cantidad = round(

                (float) $data['cantidad'],

                3

            );

            $stockResultante = match (
                $data['tipo_movimiento']
            ) {

                MovimientoInventario::ENTRADA,
                MovimientoInventario::AJUSTE_ENTRADA
                    => $stockAnterior + $cantidad,

                MovimientoInventario::SALIDA,
                MovimientoInventario::AJUSTE_SALIDA
                    => $stockAnterior - $cantidad

            };

            $stockResultante = round(
                $stockResultante,
                3
            );

            if ($stockResultante < 0) {

                throw ValidationException::withMessages([

                    'cantidad' => [

                        'La cantidad solicitada supera el stock disponible.'

                    ]

                ]);

            }

            $data['codigo_movimiento'] =
                'MOV-' . Str::upper(
                    (string) Str::ulid()
                );

            $data['cantidad'] =
                $this->formatDecimal($cantidad);

            $data['stock_anterior'] =
                $this->formatDecimal($stockAnterior);

            $data['stock_resultante'] =
                $this->formatDecimal($stockResultante);

            $data['fecha_movimiento'] =
                $data['fecha_movimiento'] ?? now();

            $data['estado_registro'] = 'A';

            $this->repository->updateStock(

                $stockSucursal,

                $data['stock_resultante']

            );

            return $this->repository->create($data);

        });
    }

    /**
     * Formatea una cantidad con tres decimales.
     */
    private function formatDecimal(
        float $value
    ): string {

        return number_format(

            $value,

            3,

            '.',

            ''

        );
    }

    /**
 * Actualiza información complementaria del movimiento.
 */
public function update(
    MovimientoInventario $movimientoInventario,
    array $data
): MovimientoInventario {

    return DB::transaction(
        function () use ($movimientoInventario, $data) {

            return $this->repository->update(

                $movimientoInventario,

                $data

            );

        }
    );
}
}