<?php

namespace App\Services\Inventario;

use App\Models\Inventario\Producto;
use App\Repositories\Inventario\ProductoRepository;
use Illuminate\Support\Facades\DB;


class ProductoService
{

    /**
     * Constructor.
     */
    public function __construct(
        private readonly ProductoRepository $repository
    )
    {
    }


    /**
     * Lista productos.
     */
    public function index(
        bool $incluirInactivos = false
    )
    {

        return $this
            ->repository
            ->getAll(
                $incluirInactivos
            );

    }


    /**
     * Muestra un producto.
     */
    public function show(
        int $id
    ): Producto
    {

        return $this
            ->repository
            ->findById(
                $id
            );

    }


    /**
     * Registra un producto.
     */
    public function store(
        array $data
    ): Producto
    {

        return DB::transaction(
            function () use ($data) {

                $data['estado_registro'] = 'A';


                return $this
                    ->repository
                    ->create(
                        $data
                    );

            }
        );

    }


    /**
     * Actualiza un producto.
     */
    public function update(
        Producto $producto,
        array    $data
    ): Producto
    {

        return DB::transaction(
            function () use (
                $producto,
                $data
            ) {

                return $this
                    ->repository
                    ->update(
                        $producto,
                        $data
                    );

            }
        );

    }


    /**
     * Desactivación lógica.
     */
    public function destroy(
        Producto $producto
    ): bool
    {

        return DB::transaction(
            function () use (
                $producto
            ) {

                return $this
                    ->repository
                    ->delete(
                        $producto
                    );

            }
        );

    }


    /**
     * Reactiva un producto.
     */
    public function reactivate(
        Producto $producto
    ): bool
    {

        return DB::transaction(
            function () use (
                $producto
            ) {

                return $this
                    ->repository
                    ->reactivate(
                        $producto
                    );

            }
        );

    }

}
