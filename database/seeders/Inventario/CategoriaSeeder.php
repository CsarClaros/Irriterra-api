<?php

namespace Database\Seeders\Inventario;

use App\Models\Inventario\Categoria;
use Illuminate\Database\Seeder;


class CategoriaSeeder
    extends Seeder
{

    /**
     * Ejecuta el Seeder.
     */
    public function run(): void
    {

        /*
        |--------------------------------------------------------------------------
        | Riego
        |--------------------------------------------------------------------------
        */

        $riego =
            $this->categoria(
                null,
                'Riego',
                'riego',
                'Productos y componentes para sistemas de riego.',
                10
            );


        /*
        |--------------------------------------------------------------------------
        | Riego > Tuberías
        |--------------------------------------------------------------------------
        */

        $tuberias =
            $this->categoria(
                $riego,
                'Tuberías',
                'riego-tuberias',
                'Tuberías utilizadas en sistemas de riego.',
                10
            );


        $this->categoria(
            $tuberias,
            'PVC',
            'riego-tuberias-pvc',
            null,
            10
        );


        $this->categoria(
            $tuberias,
            'HDPE',
            'riego-tuberias-hdpe',
            null,
            20
        );


        $this->categoria(
            $tuberias,
            'Politubos',
            'riego-tuberias-politubos',
            null,
            30
        );


        /*
        |--------------------------------------------------------------------------
        | Riego > Accesorios
        |--------------------------------------------------------------------------
        */

        $accesorios =
            $this->categoria(
                $riego,
                'Accesorios para sistemas de riego',
                'riego-accesorios',
                'Accesorios y conexiones para sistemas de riego.',
                20
            );


        $this->categoria(
            $accesorios,
            'PVC',
            'riego-accesorios-pvc',
            null,
            10
        );


        $this->categoria(
            $accesorios,
            'Fitting HDPE',
            'riego-accesorios-fitting-hdpe',
            null,
            20
        );


        $this->categoria(
            $accesorios,
            'Mini accesorios para riego',
            'riego-accesorios-mini',
            null,
            30
        );


        /*
        |--------------------------------------------------------------------------
        | Riego > Mangueras
        |--------------------------------------------------------------------------
        */

        $mangueras =
            $this->categoria(
                $riego,
                'Mangueras',
                'riego-mangueras',
                'Mangueras para conducción y distribución de agua.',
                30
            );


        $this->categoria(
            $mangueras,
            'Succión',
            'riego-mangueras-succion',
            null,
            10
        );


        $this->categoria(
            $mangueras,
            'Ciegas',
            'riego-mangueras-ciegas',
            null,
            20
        );


        /*
        |--------------------------------------------------------------------------
        | Otros grupos de riego
        |--------------------------------------------------------------------------
        */

        $this->categoria(
            $riego,
            'Cintas de goteo',
            'riego-cintas-goteo',
            null,
            40
        );


        $this->categoria(
            $riego,
            'Aspersores',
            'riego-aspersores',
            null,
            50
        );


        $this->categoria(
            $riego,
            'Filtros',
            'riego-filtros',
            null,
            60
        );


        $this->categoria(
            $riego,
            'Válvulas',
            'riego-valvulas',
            null,
            70
        );

        $this->categoria(
            $riego,
            'Cintas de lluvia',
            'riego-cintas-lluvia',
            'Cintas utilizadas para sistemas de riego por lluvia.',
            80
        );


        $this->categoria(
            $riego,
            'Tanques',
            'riego-tanques',
            'Tanques para almacenamiento de agua.',
            90
        );


        /*
        |--------------------------------------------------------------------------
        | Geomembranas
        |--------------------------------------------------------------------------
        */

        $this->categoria(
            null,
            'Geomembranas',
            'geomembranas',
            'Geomembranas para almacenamiento de agua, riego y aplicaciones especializadas.',
            20
        );

        $servicios =
            $this->categoria(

                null,

                'Servicios',

                'servicios',

                'Servicios de instalación, transporte y apoyo asociados a los productos y sistemas comercializados por Irriterra.',

                40

            );

        /*
        |--------------------------------------------------------------------------
        | Maquinaria
        |--------------------------------------------------------------------------
        */

        $maquinaria =
            $this->categoria(
                null,
                'Maquinaria',
                'maquinaria',
                'Equipos y maquinaria para los sectores agropecuario y productivo.',
                30
            );


        $this->categoria(
            $maquinaria,
            'Motores',
            'maquinaria-motores',
            null,
            10
        );


        $this->categoria(
            $maquinaria,
            'Motobombas',
            'maquinaria-motobombas',
            null,
            20
        );


        $this->categoria(
            $maquinaria,
            'Motocultivadores',
            'maquinaria-motocultivadores',
            null,
            30
        );

        $this->categoria(
            $maquinaria,
            'Bombas eléctricas',
            'maquinaria-bombas-electricas',
            null,
            40
        );


        $this->categoria(
            $maquinaria,
            'Generadores',
            'maquinaria-generadores',
            null,
            50
        );


    }


    /*
    |--------------------------------------------------------------------------
    | Crear o actualizar categoría
    |--------------------------------------------------------------------------
    */

    private function categoria(
        ?Categoria $padre,
        string     $nombre,
        string     $slug,
        ?string    $descripcion,
        int        $orden
    ): Categoria
    {

        return Categoria::updateOrCreate(

            [
                'slug' =>
                    $slug
            ],

            [
                'id_categoria_padre' =>
                    $padre
                        ?->id_categoria,

                'nombre' =>
                    $nombre,

                'descripcion' =>
                    $descripcion,

                'orden' =>
                    $orden,

                'observaciones' =>
                    null,

                'estado_registro' =>
                    'A'
            ]

        );

    }

}
