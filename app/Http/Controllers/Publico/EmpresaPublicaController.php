<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Http\Resources\Publico\EmpresaPublicaResource;
use App\Services\Publico\EmpresaPublicaService;


class EmpresaPublicaController
    extends Controller
{

    public function __construct(
        private readonly EmpresaPublicaService $empresaPublicaService
    )
    {
    }


    /*
    |--------------------------------------------------------------------------
    | Empresa pública
    |--------------------------------------------------------------------------
    */

    public function show():
    EmpresaPublicaResource
    {

        return new EmpresaPublicaResource(

            $this
                ->empresaPublicaService
                ->obtener()

        );

    }

}
