<?php

namespace App\Console\Commands;

use App\Services\Importacion\CatalogoExcelImporter;
use Illuminate\Console\Command;
use Throwable;


class ImportarCatalogoCommand
    extends Command
{

    protected $signature =
        'irriterra:importar-catalogo
    {archivo : Ruta al archivo XLSX}
    {--dry-run : Analiza el catálogo sin modificar la base de datos}
    {--importar : Ejecuta la importación real en la base de datos}
    {--reporte : Genera un CSV con el detalle del análisis}';


    protected $description =
        'Analiza e importa el catálogo inicial de productos de Irriterra.';


    public function handle(
        CatalogoExcelImporter $importer
    ): int
    {

        /*
        |--------------------------------------------------------------------------
        | Modo de ejecución
        |--------------------------------------------------------------------------
        */

        $dryRun =
            (bool)
            $this->option(
                'dry-run'
            );


        $importar =
            (bool)
            $this->option(
                'importar'
            );


        /*
        |--------------------------------------------------------------------------
        | Debe elegirse exactamente un modo
        |--------------------------------------------------------------------------
        */

        if (
            $dryRun === $importar
        ) {

            $this->error(
                'Debe utilizar exactamente una opción: --dry-run o --importar.'
            );


            return self::FAILURE;

        }


        $ruta =
            (string)
            $this->argument(
                'archivo'
            );


        if (
            !str_starts_with(
                $ruta,
                DIRECTORY_SEPARATOR
            )
        ) {

            $ruta =
                base_path(
                    $ruta
                );

        }


        try {

            $this->info(
                'Analizando catálogo de Irriterra...'
            );

            /*
|--------------------------------------------------------------------------
| Importación real
|--------------------------------------------------------------------------
*/

            if (
                $importar
            ) {

                $this->warn(
                    'Se modificará la base de datos.'
                );


                if (
                    !$this->confirm(
                        '¿Desea ejecutar la importación del catálogo?'
                    )
                ) {

                    $this->comment(
                        'Importación cancelada.'
                    );


                    return self::SUCCESS;

                }


                $resultado =
                    $importer->importar(
                        $ruta
                    );


                $this->newLine();


                $this->table(

                    [
                        'Resultado',
                        'Cantidad'
                    ],

                    [

                        [
                            'Filas importadas',
                            $resultado['filas_importadas']
                        ],

                        [
                            'Productos creados',
                            $resultado['productos_creados']
                        ],

                        [
                            'Productos existentes',
                            $resultado['productos_existentes']
                        ],

                        [
                            'Variantes creadas',
                            $resultado['variantes_creadas']
                        ],

                        [
                            'Variantes actualizadas',
                            $resultado['variantes_actualizadas']
                        ],

                        [
                            'Precios creados',
                            $resultado['precios_creados']
                        ],

                        [
                            'Precios actualizados',
                            $resultado['precios_actualizados']
                        ],

                        [
                            'Precios sin cambios',
                            $resultado['precios_sin_cambios']
                        ],

                        [
                            'Filas sin precio importable',
                            $resultado['filas_sin_precio']
                        ],

                        [
                            'Filas pendientes de revisión',
                            $resultado['filas_revision']
                        ]

                    ]

                );


                $this->newLine();


                $this->info(
                    'Importación completada correctamente.'
                );


                return self::SUCCESS;

            }


            $analisis =
                $importer
                    ->analizar(
                        $ruta
                    );


            $resumen =
                $analisis['resumen'];


            $this->newLine();


            $this->table(

                [
                    'Concepto',
                    'Cantidad'
                ],

                [

                    [
                        'Filas del catálogo',
                        $resumen['filas']
                    ],

                    [
                        'Productos propuestos',
                        $resumen['productos_propuestos']
                    ],

                    [
                        'Variantes propuestas',
                        $resumen['variantes_propuestas']
                    ],

                    [
                        'Filas agrupadas automáticamente',
                        $resumen['filas_agrupadas']
                    ],

                    [
                        'Precios válidos',
                        $resumen['precios_validos']
                    ],

                    [
                        'Sin precio',
                        $resumen['sin_precio']
                    ],

                    [
                        'Precios para revisar',
                        $resumen['precios_revision']
                    ],

                    [
                        'Sin marca',
                        $resumen['marcas_sin_marca']
                    ],

                    [
                        'Marcas para revisar',
                        $resumen['marcas_revision']
                    ],

                    [
                        'Unidades ausentes',
                        $resumen['unidades_ausentes']
                    ],

                    [
                        'Sin categoría',
                        $resumen['sin_categoria']
                    ],

                    [
                        'Códigos duplicados',
                        $resumen['codigos_duplicados']
                    ],

                    [
                        'Filas que requieren revisión',
                        $resumen['requieren_revision']
                    ],

                    [
                        'Filas excluidas del catálogo',
                        $resumen['filas_omitidas']
                    ],

                    [
                        'Filas que bloquean importación',
                        $resumen['bloquean_importacion']
                    ],

                ]

            );


            /*
            |--------------------------------------------------------------------------
            | Categorías
            |--------------------------------------------------------------------------
            */

            $this->newLine();


            $this->info(
                'Distribución propuesta por categoría:'
            );


            $categorias = [];


            foreach (
                $resumen['categorias']
                as $categoria => $cantidad
            ) {

                $categorias[] = [

                    $categoria,

                    $cantidad

                ];

            }


            $this->table(

                [
                    'Categoría',
                    'Filas'
                ],

                $categorias

            );


            /*
            |--------------------------------------------------------------------------
            | Códigos duplicados
            |--------------------------------------------------------------------------
            */

            if (
                count(
                    $analisis['duplicados']
                )
                > 0
            ) {

                $this->newLine();


                $this->warn(
                    'Códigos comerciales repetidos:'
                );


                $duplicados = [];


                foreach (
                    $analisis['duplicados']
                    as $codigo => $filas
                ) {

                    $duplicados[] = [

                        $codigo,

                        implode(
                            ', ',
                            $filas
                        )

                    ];

                }


                $this->table(

                    [
                        'Código',
                        'Filas Excel'
                    ],

                    $duplicados

                );

            }


            /*
            |--------------------------------------------------------------------------
            | Reporte
            |--------------------------------------------------------------------------
            */

            if (
                $this->option(
                    'reporte'
                )
            ) {

                $rutaReporte =
                    $importer
                        ->generarReporte(
                            $analisis
                        );


                $this->newLine();


                $this->info(
                    'Reporte generado:'
                );


                $this->line(
                    $rutaReporte
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Resultado
            |--------------------------------------------------------------------------
            */

            $this->newLine();


            if (
                $resumen['requieren_revision']
                > 0
            ) {

                $this->warn(
                    'La simulación terminó, pero existen registros que deben revisarse.'
                );

            } else {

                $this->info(
                    'La simulación terminó sin incidencias.'
                );

            }


            $this->comment(
                'No se realizó ningún cambio en la base de datos.'
            );


            return self::SUCCESS;

        } catch (
        Throwable $e
        ) {

            $this->error(
                $e->getMessage()
            );


            return self::FAILURE;

        }

    }

}
