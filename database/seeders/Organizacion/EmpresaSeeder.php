<?php

namespace Database\Seeders\Organizacion;

use App\Models\Organizacion\Empresa;
use Illuminate\Database\Seeder;
use RuntimeException;


class EmpresaSeeder extends Seeder
{

    /**
     * Inicializa la empresa principal
     * únicamente cuando aún no existe.
     */
    public function run(): void
    {

        /*
        |--------------------------------------------------------------------------
        | La empresa ya existe
        |--------------------------------------------------------------------------
        |
        | No se sobrescriben posteriormente
        | los datos administrados desde el ERP.
        |
        */

        $empresa =
            Empresa::query()
                ->orderBy(
                    'id_empresa'
                )
                ->first();


        if (
            $empresa
        ) {

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | NIT requerido únicamente para creación
        |--------------------------------------------------------------------------
        */

        $nit =
            trim(
                (string)
                config(
                    'irriterra.empresa.nit'
                )
            );


        if (
            $nit === ''
        ) {

            throw new RuntimeException(
                'Debe configurar IRRITERRA_NIT antes de crear la empresa.'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Crear empresa
        |--------------------------------------------------------------------------
        */

        Empresa::create([

            'nombre' =>
                'Irriterra S.R.L.',

            'nit' =>
                $nit,

            'telefono' =>
                '71289640 / 71949444',

            'correo' =>
                'info@irriterrasrl.com',

            'direccion' =>
                'Kenko, Av. Argelia, calle Mamoré, El Alto, La Paz',

            'sitio_web' =>
                'https://irriterrasrl.com',

            'logo' =>
                null,

            'observaciones' =>
                null,

            'estado_registro' =>
                'A'

        ]);

    }

}
