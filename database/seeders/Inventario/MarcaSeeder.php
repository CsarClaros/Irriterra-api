<?php

namespace Database\Seeders\Inventario;

use App\Models\Inventario\Marca;
use Illuminate\Database\Seeder;


class MarcaSeeder extends Seeder
{

    public function run(): void
    {

        $marcas = [

            ['ABRISA', 'abrisa', 'España', null],

            ['ALCON', 'alcon', null, null],

            ['AZUD', 'azud', 'España', null],

            ['BONHOFER', 'bonhofer', null, null],

            [
                'BRIGS STRATTON',
                'brigs-stratton',
                null,
                'Nombre conservado según el archivo fuente; pendiente de validación.'
            ],

            ['CAMPEON', 'campeon', 'Bolivia', null],

            ['CAMSCO', 'camsco', null, null],

            [
                'CITY PUMS',
                'city-pums',
                'Italia',
                'Nombre conservado según el archivo fuente; pendiente de validación.'
            ],

            ['DAEWOO', 'daewoo', null, null],

            ['GOLDEN SPRAY', 'golden-spray', null, null],

            ['GR', 'gr', null, null],

            ['GREEN PLAINS', 'green-plains', null, null],

            ['HONDA', 'honda', null, null],

            ['IRRITIME', 'irritime', 'Turquía', null],

            [
                'KHOLER',
                'kholer',
                null,
                'Nombre conservado según el archivo fuente; pendiente de validación.'
            ],

            ['KRONA', 'krona', null, null],

            ['LT', 'lt', null, null],

            ['MULLER', 'muller', null, null],

            ['NAANDAN JAIN', 'naandan-jain', null, null],

            ['NETAFIM', 'netafim', null, null],

            ['NUEVA ERA', 'nueva-era', null, null],

            ['PAVCO', 'pavco', 'Colombia', null],

            ['PLASSON', 'plasson', null, null],

            ['POELSAN', 'poelsan', 'Turquía', null],

            ['PQA', 'pqa', null, null],

            ['SANKING', 'sanking', null, null],

            [
                'SECTORIAL JOLLY',
                'sectorial-jolly',
                null,
                'Clasificado como marca según el archivo fuente; pendiente de validación.'
            ],

            ['SENNINGER', 'senninger', null, null],

            ['STF', 'stf', 'Turquía', null],

            ['SUNSTREAM', 'sunstream', 'Turquía', null],

            ['TERMOPLAST', 'termoplast', null, null],

            ['TIGRE', 'tigre', null, null],

            ['TUPY', 'tupy', null, null],

            ['UNIRAIN', 'unirain', null, null]

        ];


        foreach (
            $marcas as $indice => $datos
        ) {

            [
                $nombre,
                $slug,
                $pais,
                $observaciones
            ] = $datos;


            Marca::firstOrCreate(

                [
                    'slug' =>
                        $slug
                ],

                [
                    'nombre' =>
                        $nombre,

                    'pais' =>
                        $pais,

                    'logo' =>
                        null,

                    'sitio_web' =>
                        null,

                    'orden' =>
                        ($indice + 1) * 10,

                    'observaciones' =>
                        $observaciones,

                    'estado_registro' =>
                        'A'

                ]

            );

        }

    }

}
