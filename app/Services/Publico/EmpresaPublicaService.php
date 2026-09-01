<?php

namespace App\Services\Publico;

use App\Models\Organizacion\Empresa;


class EmpresaPublicaService
{

    /*
    |--------------------------------------------------------------------------
    | Obtener empresa pública
    |--------------------------------------------------------------------------
    */

    public function obtener():
    Empresa
    {

        return Empresa::query()
            ->where(
                'estado_registro',
                'A'
            )
            ->orderBy(
                'id_empresa'
            )
            ->firstOrFail();

    }

}
