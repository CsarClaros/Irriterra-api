<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Organizacion\EmpresaController;
use App\Http\Controllers\Organizacion\SucursalController;

/*
|--------------------------------------------------------------------------
| Módulo Organización
|--------------------------------------------------------------------------
|
| Empresa
| Sucursal
|
*/

Route::apiResource(
    'empresa',
    EmpresaController::class
);

Route::apiResource(
    'sucursal',
    SucursalController::class
);