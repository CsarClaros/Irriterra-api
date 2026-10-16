<?php

namespace App\Services\Inventario;

use App\Models\Inventario\StockSucursal;
use App\Repositories\Inventario\StockSucursalRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockSucursalService
{
    public function __construct(
        private readonly StockSucursalRepository $repository
    )
    {
    }

    public function index()
    {
        return $this->repository->getAll();
    }

    public function show(
        int $id
    ): StockSucursal
    {

        return $this->repository
            ->findById($id);
    }

    public function store(
        array $data
    ): StockSucursal
    {

        return DB::transaction(
            function () use ($data) {

                /*
                 * Un stock nuevo siempre comienza
                 * en cero.
                 *
                 * Las existencias deben entrar por
                 * movimientos de inventario.
                 */
                if (
                    array_key_exists(
                        'stock_actual',
                        $data
                    )
                    &&
                    round(
                        (float)
                        $data['stock_actual'],
                        3
                    ) !== 0.0
                ) {

                    throw ValidationException::withMessages([

                        'stock_actual' => [

                            'El stock inicial debe ser cero. Utilice un movimiento de inventario para registrar existencias.'

                        ]

                    ]);
                }

                $data['stock_actual'] =
                    '0.000';

                $data['stock_minimo'] =
                    $data['stock_minimo']
                    ?? 0;

                $data['estado_registro'] = 'A';

                return $this->repository
                    ->create($data);
            }
        );
    }

    public function update(
        StockSucursal $stockSucursal,
        array         $data
    ): StockSucursal
    {

        return DB::transaction(
            function () use (
                $stockSucursal,
                $data
            ) {

                /*
                 * Sucursal y variante forman
                 * la identidad histórica del stock.
                 */
                if (
                    array_key_exists(
                        'id_sucursal',
                        $data
                    )
                    &&
                    (int)$data['id_sucursal']
                    !==
                    (int)$stockSucursal
                        ->id_sucursal
                ) {

                    throw ValidationException::withMessages([

                        'id_sucursal' => [

                            'La sucursal de un registro de stock no puede modificarse.'

                        ]

                    ]);
                }

                if (
                    array_key_exists(
                        'id_producto_variante',
                        $data
                    )
                    &&
                    (int)
                    $data['id_producto_variante']
                    !==
                    (int)
                    $stockSucursal
                        ->id_producto_variante
                ) {

                    throw ValidationException::withMessages([

                        'id_producto_variante' => [

                            'La variante de un registro de stock no puede modificarse.'

                        ]

                    ]);
                }

                /*
                 * stock_actual solamente puede
                 * cambiar mediante MovimientoInventario.
                 */
                if (
                    array_key_exists(
                        'stock_actual',
                        $data
                    )
                    &&
                    round(
                        (float)
                        $data['stock_actual'],
                        3
                    )
                    !==
                    round(
                        (float)
                        $stockSucursal
                            ->stock_actual,
                        3
                    )
                ) {

                    throw ValidationException::withMessages([

                        'stock_actual' => [

                            'El stock actual no puede modificarse directamente. Utilice un movimiento de inventario.'

                        ]

                    ]);
                }

                /*
                 * Permitimos temporalmente que el
                 * frontend siga enviando estos campos
                 * sin modificarlos.
                 */
                unset(

                    $data['id_sucursal'],

                    $data['id_producto_variante'],

                    $data['stock_actual']

                );

                return $this->repository
                    ->update(

                        $stockSucursal,

                        $data

                    );
            }
        );
    }

    public function destroy(
        StockSucursal $stockSucursal
    ): bool
    {

        return DB::transaction(
            function () use (
                $stockSucursal
            ) {

                return $this->repository
                    ->delete(
                        $stockSucursal
                    );
            }
        );
    }
}
