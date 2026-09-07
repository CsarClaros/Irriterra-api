<?php

namespace App\Repositories\Inventario;

use App\Models\Inventario\ProductoDocumento;
use Illuminate\Database\Eloquent\Collection;


class ProductoDocumentoRepository
{

    public function getAll(
        bool $incluirInactivos = false
    ): Collection
    {

        $query =
            ProductoDocumento::query()
                ->with([
                    'producto:id_producto,nombre'
                ]);


        if (
            !$incluirInactivos
        ) {

            $query->where(
                'estado_registro',
                'A'
            );

        }


        return $query
            ->orderByRaw(
                "CASE
                    WHEN estado_registro = 'A'
                    THEN 0
                    ELSE 1
                END"
            )
            ->orderBy(
                'id_producto'
            )
            ->orderBy(
                'orden'
            )
            ->orderBy(
                'nombre'
            )
            ->get();

    }


    public function findById(
        int $id
    ): ProductoDocumento
    {

        return ProductoDocumento::query()
            ->with([
                'producto:id_producto,nombre'
            ])
            ->findOrFail(
                $id
            );

    }


    public function create(
        array $data
    ): ProductoDocumento
    {

        $documento =
            ProductoDocumento::create(
                $data
            );


        return $documento->load([
            'producto:id_producto,nombre'
        ]);

    }


    public function update(
        ProductoDocumento $documento,
        array             $data
    ): ProductoDocumento
    {

        $documento->update(
            $data
        );


        return $documento->fresh([
            'producto:id_producto,nombre'
        ]);

    }


    public function delete(
        ProductoDocumento $documento
    ): bool
    {

        return $documento->update([

            'estado_registro' =>
                'I'

        ]);

    }


    public function reactivate(
        ProductoDocumento $documento
    ): bool
    {

        return $documento->update([

            'estado_registro' =>
                'A'

        ]);

    }

}
