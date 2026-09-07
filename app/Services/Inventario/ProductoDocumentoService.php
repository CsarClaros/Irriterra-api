<?php

namespace App\Services\Inventario;

use App\Models\Inventario\ProductoDocumento;
use App\Repositories\Inventario\ProductoDocumentoRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;


class ProductoDocumentoService
{

    public function __construct(
        private readonly ProductoDocumentoRepository $repository
    )
    {
    }


    public function index(
        bool $incluirInactivos = false
    ): Collection
    {

        return $this->repository
            ->getAll(
                $incluirInactivos
            );

    }


    public function show(
        int $id
    ): ProductoDocumento
    {

        return $this->repository
            ->findById(
                $id
            );

    }


    public function store(
        array $data
    ): ProductoDocumento
    {

        return DB::transaction(
            function () use ($data) {

                $data['es_publico'] =
                    array_key_exists(
                        'es_publico',
                        $data
                    )

                        ? (bool)
                    $data['es_publico']

                        : true;


                $data['orden'] =
                    (int)
                    (
                        $data['orden']
                        ?? 0
                    );


                $data['estado_registro'] =
                    'A';


                return $this->repository
                    ->create(
                        $data
                    );

            }
        );

    }


    public function update(
        ProductoDocumento $documento,
        array             $data
    ): ProductoDocumento
    {

        return DB::transaction(
            function () use (
                $documento,
                $data
            ) {

                if (
                    array_key_exists(
                        'es_publico',
                        $data
                    )
                ) {

                    $data['es_publico'] =
                        (bool)
                        $data['es_publico'];

                }


                if (
                    array_key_exists(
                        'orden',
                        $data
                    )
                ) {

                    $data['orden'] =
                        (int)
                        $data['orden'];

                }


                return $this->repository
                    ->update(

                        $documento,

                        $data

                    );

            }
        );

    }


    public function destroy(
        ProductoDocumento $documento
    ): bool
    {

        return DB::transaction(
            fn() => $this->repository
                ->delete(
                    $documento
                )
        );

    }


    public function reactivate(
        ProductoDocumento $documento
    ): bool
    {

        return DB::transaction(
            fn() => $this->repository
                ->reactivate(
                    $documento
                )
        );

    }

}
