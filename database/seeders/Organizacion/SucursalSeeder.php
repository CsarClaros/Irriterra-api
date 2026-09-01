<?php

namespace Database\Seeders\Organizacion;

use Illuminate\Database\Seeder;
use App\Models\Organizacion\Empresa;
use App\Models\Organizacion\Sucursal;

class SucursalSeeder extends Seeder
{
    /**
     * Ejecuta el Seeder del módulo Sucursal.
     */
    public function run(): void
    {
        $empresa = Empresa::where('nit', '0')->first();

        if (!$empresa) {
            return;
        }

        Sucursal::updateOrCreate(

        /*
        |--------------------------------------------------------------------------
        | Registro de búsqueda
        |--------------------------------------------------------------------------
        */

            [
                'codigo' => 'SC-001'
            ],

            /*
            |--------------------------------------------------------------------------
            | Datos
            |--------------------------------------------------------------------------
            */

            [

                'id_empresa' => $empresa->id_empresa,

                'nombre' => 'Casa Matriz',

                'departamento' => 'La Paz',

                'ciudad' => 'La Paz',

                'direccion' => 'Pendiente de actualizar',

                'telefono' => null,

                'correo' => null,

                'url_maps' =>
                    null,

                'url_maps_embed' =>
                    null,

                'observaciones' => 'Sucursal principal del ERP.',

                'estado_registro' => 'A'

            ]

        );
    }
}
