<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Http\Resources\Publico\SucursalPublicaResource;
use App\Services\Publico\SucursalPublicaService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class SucursalPublicaController
    extends Controller
{

    public function __construct(
        private readonly SucursalPublicaService $sucursalPublicaService
    )
    {
    }


    public function index():
    AnonymousResourceCollection
    {

        return SucursalPublicaResource::collection(

            $this
                ->sucursalPublicaService
                ->listar()

        );

    }

}
