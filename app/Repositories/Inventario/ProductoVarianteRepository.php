<?php

namespace App\Repositories\Inventario;

use App\Models\Inventario\ProductoVariante;
use Illuminate\Database\Eloquent\Collection;


class ProductoVarianteRepository
{

    /**
     * Lista variantes activas.
     */
    public function getAll()
    {

        return ProductoVariante::with([

            'producto.categoria',

            'marca'

        ])
            ->where(
                'estado_registro',
                'A'
            )
            ->orderBy(
                'sku'
            )
            ->get();

    }


    /*
|--------------------------------------------------------------------------
| Buscar variantes para selección
|--------------------------------------------------------------------------
*/

    public function search(
        string $buscar,
        int $limite = 20
    ): Collection
    {
        $buscar =
            trim(
                $buscar
            );

        $patron =
            '%' . $buscar . '%';

        $prefijo =
            $buscar . '%';

        $limite =
            max(
                1,
                min(
                    $limite,
                    20
                )
            );


        return ProductoVariante::query()

            ->with([

                'producto:id_producto,nombre,modelo,marca,estado_registro',

                'marca:id_marca,nombre,estado_registro'

            ])

            ->where(
                'producto_variante.estado_registro',
                'A'
            )

            /*
            |--------------------------------------------------------------------------
            | El producto propietario también debe estar activo.
            |--------------------------------------------------------------------------
            */

            ->whereHas(
                'producto',
                function ($query): void {

                    $query->where(
                        'estado_registro',
                        'A'
                    );

                }
            )

            /*
            |--------------------------------------------------------------------------
            | Campos de búsqueda
            |--------------------------------------------------------------------------
            */

            ->where(
                function ($query) use (
                    $patron
                ): void {

                    $query

                        ->where(
                            'sku',
                            'like',
                            $patron
                        )

                        ->orWhere(
                            'codigo_comercial',
                            'like',
                            $patron
                        )

                        ->orWhere(
                            'nombre',
                            'like',
                            $patron
                        )

                        ->orWhereHas(
                            'producto',
                            function ($producto) use (
                                $patron
                            ): void {

                                $producto

                                    ->where(
                                        'nombre',
                                        'like',
                                        $patron
                                    )

                                    ->orWhere(
                                        'modelo',
                                        'like',
                                        $patron
                                    )

                                    /*
                                     * Compatibilidad temporal
                                     * con producto.marca.
                                     */
                                    ->orWhere(
                                        'marca',
                                        'like',
                                        $patron
                                    );

                            }
                        )

                        ->orWhereHas(
                            'marca',
                            function ($marca) use (
                                $patron
                            ): void {

                                $marca
                                    ->where(
                                        'estado_registro',
                                        'A'
                                    )
                                    ->where(
                                        'nombre',
                                        'like',
                                        $patron
                                    );

                            }
                        );

                }
            )

            /*
            |--------------------------------------------------------------------------
            | Priorizar coincidencias útiles.
            |--------------------------------------------------------------------------
            */

            ->orderByRaw(
                '
                CASE
                    WHEN sku = ? THEN 0
                    WHEN codigo_comercial = ? THEN 1
                    WHEN sku LIKE ? THEN 2
                    WHEN codigo_comercial LIKE ? THEN 3
                    ELSE 4
                END
            ',
                [
                    $buscar,
                    $buscar,
                    $prefijo,
                    $prefijo
                ]
            )

            ->orderBy(
                'sku'
            )

            ->limit(
                $limite
            )

            ->get();
    }

    /**
     * Busca por ID.
     */
    public function findById(
        int $id
    ): ProductoVariante
    {

        return ProductoVariante::with([

            'producto.categoria',

            'marca'

        ])
            ->findOrFail(
                $id
            );

    }


    /**
     * Registra una variante.
     */
    public function create(
        array $data
    ): ProductoVariante
    {

        $productoVariante =
            ProductoVariante::create(
                $data
            );


        return $productoVariante
            ->load([

                'producto.categoria',

                'marca'

            ]);

    }


    /**
     * Actualiza una variante.
     */
    public function update(
        ProductoVariante $productoVariante,
        array            $data
    ): ProductoVariante
    {

        $productoVariante->update(
            $data
        );


        return $productoVariante
            ->fresh([

                'producto.categoria',

                'marca'

            ]);

    }


    /**
     * Eliminación lógica.
     */
    public function delete(
        ProductoVariante $productoVariante
    ): bool
    {

        return $productoVariante
            ->update([

                'estado_registro' =>
                    'I'

            ]);

    }

}
