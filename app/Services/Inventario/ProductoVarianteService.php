<?php

namespace App\Services\Inventario;

use App\Models\Inventario\MovimientoInventario;
use App\Models\Inventario\Producto;
use App\Models\Inventario\ProductoVariante;
use App\Models\Inventario\StockSucursal;
use App\Repositories\Inventario\ProductoVarianteRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Collection;

class ProductoVarianteService
{
    public function __construct(
        private readonly ProductoVarianteRepository $repository
    )
    {
    }

    public function index()
    {
        return $this->repository->getAll();
    }

    /*
|--------------------------------------------------------------------------
| Buscar
|--------------------------------------------------------------------------
*/

    public function search(
        string $buscar
    ): Collection
    {
        return $this->repository
            ->search(
                $buscar,
                20
            );
    }

    public function show(
        int $id
    ): ProductoVariante
    {

        return $this->repository
            ->findById($id);
    }

    public function store(
        array $data
    ): ProductoVariante
    {

        return DB::transaction(
            function () use ($data) {

                /*
                 * El backend es la única autoridad
                 * para crear SKU manuales.
                 */
                unset(
                    $data['sku']
                );

                $data['tipo_control_inventario'] =
                    $this
                        ->resolverTipoControl(
                            $data
                        );

                $data['sku'] =
                    $this->generarSku();

                $data['estado_registro'] = 'A';

                return $this->repository
                    ->create($data);
            }
        );
    }

    public function update(
        ProductoVariante $productoVariante,
        array            $data
    ): ProductoVariante
    {

        return DB::transaction(
            function () use (
                $productoVariante,
                $data
            ) {

                /*
                 * El SKU nunca se modifica.
                 */
                unset(
                    $data['sku']
                );

                if (
                    array_key_exists(
                        'tipo_control_inventario',
                        $data
                    )
                ) {

                    $this
                        ->validarCambioTipoControl(
                            $productoVariante,
                            $data['tipo_control_inventario']
                        );
                }

                return $this->repository
                    ->update(

                        $productoVariante,

                        $data

                    );
            }
        );
    }

    public function destroy(
        ProductoVariante $productoVariante
    ): bool
    {

        return DB::transaction(
            function () use (
                $productoVariante
            ) {

                return $this->repository
                    ->delete(
                        $productoVariante
                    );
            }
        );
    }

    private function generarSku(): string
    {
        return 'IRR-'
            . Str::upper(
                (string)Str::ulid()
            );
    }

    private function resolverTipoControl(
        array $data
    ): string
    {

        if (
            isset(
                $data['tipo_control_inventario']
            )
        ) {

            return $data['tipo_control_inventario'];
        }

        /*
         * Compatibilidad temporal con
         * producto.controla_stock.
         */
        $producto =
            Producto::query()
                ->select([
                    'id_producto',
                    'controla_stock'
                ])
                ->findOrFail(
                    $data['id_producto']
                );

        return $producto->controla_stock

            ? ProductoVariante::CANTIDAD

            : ProductoVariante::SIN_CONTROL;
    }

    private function validarCambioTipoControl(
        ProductoVariante $productoVariante,
        string           $nuevoTipo
    ): void
    {

        if (
            $nuevoTipo
            ===
            $productoVariante
                ->tipo_control_inventario
        ) {

            return;
        }

        /*
         * No se permite cambiar la naturaleza
         * del inventario después de comenzar
         * a utilizar la variante.
         */
        $tieneStock =
            StockSucursal::query()
                ->where(
                    'id_producto_variante',
                    $productoVariante
                        ->id_producto_variante
                )
                ->where(
                    'stock_actual',
                    '<>',
                    0
                )
                ->exists();

        if ($tieneStock) {

            throw ValidationException::withMessages([

                'tipo_control_inventario' => [

                    'No puede cambiarse el tipo de control de inventario porque la variante posee stock.'

                ]

            ]);
        }

        $tieneMovimientos =
            MovimientoInventario::query()
                ->whereHas(
                    'stockSucursal',
                    function ($query) use (
                        $productoVariante
                    ) {

                        $query->where(
                            'id_producto_variante',
                            $productoVariante
                                ->id_producto_variante
                        );
                    }
                )
                ->exists();

        if ($tieneMovimientos) {

            throw ValidationException::withMessages([

                'tipo_control_inventario' => [

                    'No puede cambiarse el tipo de control de inventario porque la variante ya posee movimientos históricos.'

                ]

            ]);
        }
    }
}
