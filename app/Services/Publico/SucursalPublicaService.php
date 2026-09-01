<?php

namespace App\Services\Publico;

use App\Models\Organizacion\Sucursal;
use Illuminate\Database\Eloquent\Collection;


class SucursalPublicaService
{

    public function listar():
    Collection
    {

        return Sucursal::query()
            ->where(
                'estado_registro',
                'A'
            )
            ->orderBy(
                'id_sucursal'
            )
            ->get();

    }

}
