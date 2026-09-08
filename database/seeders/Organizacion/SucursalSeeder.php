<?php

namespace Database\Seeders\Organizacion;

use App\Models\Organizacion\Empresa;
use App\Models\Organizacion\Sucursal;
use Illuminate\Database\Seeder;
use RuntimeException;


class SucursalSeeder extends Seeder
{

    /**
     * Inicializa la Casa Matriz
     * únicamente cuando aún no existe.
     */
    public function run(): void
    {

        /*
        |--------------------------------------------------------------------------
        | Empresa
        |--------------------------------------------------------------------------
        */

        $empresa =
            Empresa::query()
                ->orderBy(
                    'id_empresa'
                )
                ->first();


        if (
            !$empresa
        ) {

            throw new RuntimeException(
                'No se encontró la empresa Irriterra S.R.L.'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Casa Matriz
        |--------------------------------------------------------------------------
        */

        Sucursal::firstOrCreate(

            [
                'codigo' =>
                    'SC-001'
            ],

            [
                'id_empresa' =>
                    $empresa->id_empresa,

                'nombre' =>
                    'Casa Matriz',

                'departamento' =>
                    'La Paz',

                'ciudad' =>
                    'El Alto',

                'direccion' =>
                    'Kenko, Av. Argelia, calle Mamoré',

                'telefono' =>
                    '71289640 / 71949444',

                'correo' =>
                    'info@irriterrasrl.com',

                'url_maps' =>
                    'https://maps.app.goo.gl/FMV4m9QyyXA8K7fF6',

                'url_maps_embed' =>
                    'https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d543.0696436328201!2d-68.19272764323624!3d-16.554569115644714!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sbo!4v1788729984121!5m2!1ses-419!2sbo',

                'observaciones' =>
                    null,

                'estado_registro' =>
                    'A'

            ]

        );

    }

}
