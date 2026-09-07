<?php

namespace App\Services\Importacion;

use App\Models\Inventario\Categoria;
use App\Models\Inventario\Marca;
use App\Models\Inventario\PrecioProductoVariante;
use App\Models\Inventario\Producto;
use App\Models\Inventario\ProductoVariante;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\IOFactory;
use RuntimeException;


class CatalogoExcelImporter
{

    public function __construct(
        private readonly CatalogoNormalizer $normalizer,
        private readonly CatalogoClassifier $classifier
    )
    {
    }


    /*
    |--------------------------------------------------------------------------
    | Analizar
    |--------------------------------------------------------------------------
    */

    public function analizar(
        string $ruta
    ): array
    {

        if (
            !is_file(
                $ruta
            )
        ) {

            throw new RuntimeException(
                'No se encontró el archivo: '
                . $ruta
            );

        }


        $reader =
            IOFactory::createReaderForFile(
                $ruta
            );


        $reader->setReadDataOnly(
            true
        );


        $spreadsheet =
            $reader->load(
                $ruta
            );


        $hoja =
            $spreadsheet
                ->getSheetByName(
                    'LISTA DE PRODUCTOS'
                )
            ??
            $spreadsheet
                ->getActiveSheet();


        /*
        |--------------------------------------------------------------------------
        | Validar encabezados
        |--------------------------------------------------------------------------
        */

        $esperados = [

            'A4' =>
                'ID PRODUCTO',

            'B4' =>
                'ITEM',

            'C4' =>
                'PRECIOS',

            'D4' =>
                'MARCA',

            'E4' =>
                'MEDIDA'

        ];


        foreach (
            $esperados
            as $celda => $esperado
        ) {

            $valor =
                mb_strtoupper(
                    trim(
                        (string)
                        $hoja
                            ->getCell(
                                $celda
                            )
                            ->getValue()
                    ),
                    'UTF-8'
                );


            if (
                $valor !== $esperado
            ) {

                throw new RuntimeException(
                    "Encabezado inesperado en {$celda}: {$valor}"
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Leer filas
        |--------------------------------------------------------------------------
        */

        $filas = [];


        $ultimaFila =
            $hoja
                ->getHighestDataRow();


        for (
            $numeroFila = 5;
            $numeroFila <= $ultimaFila;
            $numeroFila++
        ) {

            $codigo =
                $hoja
                    ->getCell(
                        "A{$numeroFila}"
                    )
                    ->getValue();


            $item =
                $hoja
                    ->getCell(
                        "B{$numeroFila}"
                    )
                    ->getValue();


            $precio =
                $hoja
                    ->getCell(
                        "C{$numeroFila}"
                    )
                    ->getValue();


            $marca =
                $hoja
                    ->getCell(
                        "D{$numeroFila}"
                    )
                    ->getValue();


            $unidad =
                $hoja
                    ->getCell(
                        "E{$numeroFila}"
                    )
                    ->getValue();


            /*
             * Fila completamente vacía.
             */
            if (
                $this->filaVacia([

                    $codigo,
                    $item,
                    $precio,
                    $marca,
                    $unidad

                ])
            ) {

                continue;

            }


            $fila =
                $this->normalizer
                    ->normalizarFila(

                        $numeroFila,

                        $codigo,

                        $item,

                        $precio,

                        $marca,

                        $unidad

                    );


            $fila =
                $this->classifier
                    ->clasificar(
                        $fila
                    );


            $filas[] =
                $fila;

        }


        /*
        |--------------------------------------------------------------------------
        | Detectar códigos duplicados
        |--------------------------------------------------------------------------
        */

        $codigos = [];


        foreach (
            $filas
            as $indice => $fila
        ) {

            $codigo =
                $fila['codigo_comercial'];


            if (
                $codigo === null
            ) {

                continue;

            }


            $codigos[$codigo][] =
                $indice;

        }


        $duplicados = [];


        foreach (
            $codigos
            as $codigo => $indices
        ) {

            if (
                count(
                    $indices
                )
                <= 1
            ) {

                continue;

            }


            $filasExcel =
                [];


            foreach (
                $indices
                as $indice
            ) {

                $filas[$indice]['codigo_duplicado'] =
                    true;


                $filasExcel[] =
                    $filas[$indice]['fila_excel'];

            }


            $duplicados[$codigo] =
                $filasExcel;

        }


        /*
        |--------------------------------------------------------------------------
        | Resumen
        |--------------------------------------------------------------------------
        */

        $resumen =
            $this->construirResumen(
                $filas,
                $duplicados
            );


        $spreadsheet
            ->disconnectWorksheets();


        unset(
            $spreadsheet
        );


        return [

            'filas' =>
                $filas,

            'duplicados' =>
                $duplicados,

            'resumen' =>
                $resumen

        ];

    }

    /*
|--------------------------------------------------------------------------
| Importar
|--------------------------------------------------------------------------
*/

    public function importar(
        string $ruta
    ): array
    {

        /*
        |--------------------------------------------------------------------------
        | Analizar nuevamente antes de escribir
        |--------------------------------------------------------------------------
        */

        $analisis =
            $this->analizar(
                $ruta
            );


        $resumen =
            $analisis['resumen'];


        if (
            $resumen['bloquean_importacion']
            > 0
        ) {

            throw new RuntimeException(
                'La importación fue cancelada porque existen filas que bloquean la importación.'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Categorías disponibles
        |--------------------------------------------------------------------------
        */

        $categorias =
            Categoria::query()
                ->where(
                    'estado_registro',
                    'A'
                )
                ->get()
                ->keyBy(
                    'slug'
                );


        /*
        |--------------------------------------------------------------------------
        | Marcas disponibles
        |--------------------------------------------------------------------------
        */

        $marcas =
            Marca::query()
                ->where(
                    'estado_registro',
                    'A'
                )
                ->get()
                ->keyBy(
                    function (
                        Marca $marca
                    ) {

                        return mb_strtoupper(
                            trim(
                                $marca->nombre
                            ),
                            'UTF-8'
                        );

                    }
                );


        /*
        |--------------------------------------------------------------------------
        | Contadores
        |--------------------------------------------------------------------------
        */

        $resultado = [

            'productos_creados' =>
                0,

            'productos_existentes' =>
                0,

            'variantes_creadas' =>
                0,

            'variantes_actualizadas' =>
                0,

            'precios_creados' =>
                0,

            'precios_actualizados' =>
                0,

            'precios_sin_cambios' =>
                0,

            'filas_sin_precio' =>
                0,

            'filas_revision' =>
                0,

            'filas_importadas' =>
                0

        ];


        /*
        |--------------------------------------------------------------------------
        | Importación transaccional
        |--------------------------------------------------------------------------
        */

        return DB::transaction(
            function () use (
                $analisis,
                $categorias,
                $marcas,
                $resultado
            ) {

                $productosCache =
                    [];


                $skusVistos =
                    [];


                foreach (
                    $analisis['filas']
                    as $fila
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Omitidas
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $fila['omitir_importacion']
                        ?? false
                    ) {

                        continue;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Seguridad
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $fila['bloquea_importacion']
                        ?? false
                    ) {

                        throw new RuntimeException(
                            'La fila '
                            . $fila['fila_excel']
                            . ' bloquea la importación.'
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Categoría
                    |--------------------------------------------------------------------------
                    */

                    $slugCategoria =
                        $fila['categoria_slug'];


                    $categoria =
                        $categorias->get(
                            $slugCategoria
                        );


                    if (
                        !$categoria
                    ) {

                        throw new RuntimeException(
                            'No se encontró la categoría '
                            . $slugCategoria
                            . ' para la fila '
                            . $fila['fila_excel']
                            . '.'
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Producto
                    |--------------------------------------------------------------------------
                    */

                    $nombreProducto =
                        trim(
                            $fila['producto_propuesto']
                        );


                    $claveProducto =
                        $categoria->id_categoria
                        . '|'
                        . mb_strtolower(
                            $nombreProducto,
                            'UTF-8'
                        );


                    if (
                        !isset(
                            $productosCache[$claveProducto]
                        )
                    ) {

                        $producto =
                            Producto::query()
                                ->where(
                                    'id_categoria',
                                    $categoria->id_categoria
                                )
                                ->where(
                                    'nombre',
                                    $nombreProducto
                                )
                                ->first();


                        if (
                            !$producto
                        ) {

                            $producto =
                                Producto::create([

                                    'id_categoria' =>
                                        $categoria->id_categoria,

                                    'nombre' =>
                                        $nombreProducto,

                                    /*
                                     * Campo heredado.
                                     * La marca real pertenece
                                     * a producto_variante.
                                     */
                                    'marca' =>
                                        null,

                                    'modelo' =>
                                        null,

                                    'descripcion' =>
                                        null,

                                    'catalogo_pdf' =>
                                        null,

                                    'controla_stock' =>
                                        (bool)
                                        $fila['controla_stock'],

                                    'observaciones' =>
                                        'Registro creado desde el catálogo inicial de Irriterra.',

                                    'estado_registro' =>
                                        'A',

                                    'usuario_creacion' =>
                                        null,

                                    'usuario_modificacion' =>
                                        null

                                ]);


                            $resultado['productos_creados']++;

                        } else {

                            /*
                             * No sobrescribimos descripción,
                             * modelo ni observaciones que
                             * posteriormente puedan editarse
                             * desde el dashboard.
                             */
                            $producto->update([

                                'controla_stock' =>
                                    (bool)
                                    $fila['controla_stock'],

                                'estado_registro' =>
                                    'A'

                            ]);


                            $resultado['productos_existentes']++;

                        }


                        $productosCache[$claveProducto] =
                            $producto;

                    } else {

                        $producto =
                            $productosCache[$claveProducto];

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Marca
                    |--------------------------------------------------------------------------
                    */

                    $idMarca =
                        null;


                    $estadoMarca =
                        $fila['marca_estado']
                        ?? 'revisar';


                    if (
                        in_array(
                            $estadoMarca,
                            [
                                'valida',
                                'normalizada'
                            ],
                            true
                        )
                        &&
                        $fila['marca_normalizada']
                        !== null
                    ) {

                        $claveMarca =
                            mb_strtoupper(
                                trim(
                                    $fila['marca_normalizada']
                                ),
                                'UTF-8'
                            );


                        $marca =
                            $marcas->get(
                                $claveMarca
                            );


                        if (
                            !$marca
                        ) {

                            throw new RuntimeException(
                                'La marca "'
                                . $fila['marca_normalizada']
                                . '" no existe en la BD. Fila '
                                . $fila['fila_excel']
                                . '.'
                            );

                        }


                        $idMarca =
                            $marca->id_marca;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | SKU estable
                    |--------------------------------------------------------------------------
                    */

                    $sku =
                        $this->generarSkuImportacion(
                            $fila
                        );


                    /*
                     * Detecta una posible colisión
                     * dentro del mismo Excel.
                     */
                    if (
                        isset(
                            $skusVistos[$sku]
                        )
                    ) {

                        throw new RuntimeException(
                            'Se generó el mismo SKU '
                            . $sku
                            . ' para las filas '
                            . $skusVistos[$sku]
                            . ' y '
                            . $fila['fila_excel']
                            . '.'
                        );

                    }


                    $skusVistos[$sku] =
                        $fila['fila_excel'];


                    /*
                    |--------------------------------------------------------------------------
                    | Variante
                    |--------------------------------------------------------------------------
                    */

                    $variante =
                        ProductoVariante::query()
                            ->where(
                                'sku',
                                $sku
                            )
                            ->first();


                    if (
                        !$variante
                    ) {

                        $variante =
                            ProductoVariante::create([

                                'id_producto' =>
                                    $producto->id_producto,

                                'id_marca' =>
                                    $idMarca,

                                'nombre' =>
                                    $fila['variante_propuesta'],

                                'sku' =>
                                    $sku,

                                'codigo_comercial' =>
                                    $fila['codigo_comercial'],

                                'unidad_medida' =>
                                    $fila['unidad_medida'],

                                'descripcion' =>
                                    $fila['descripcion_variante']
                                    ?? null,

                                'observaciones' =>
                                    $this
                                        ->construirObservacionImportacion(
                                            $fila
                                        ),

                                'estado_registro' =>
                                    'A',

                                'usuario_creacion' =>
                                    null,

                                'usuario_modificacion' =>
                                    null

                            ]);


                        $resultado['variantes_creadas']++;

                    } else {

                        /*
                         * No borramos una marca que
                         * posteriormente haya sido corregida
                         * manualmente si el Excel trae una
                         * marca ambigua.
                         */
                        $datosVariante = [

                            'id_producto' =>
                                $producto->id_producto,

                            'nombre' =>
                                $fila['variante_propuesta'],

                            'codigo_comercial' =>
                                $fila['codigo_comercial'],

                            'unidad_medida' =>
                                $fila['unidad_medida'],

                            'estado_registro' =>
                                'A'
                        ];

                        if (
                            !empty(
                            $fila['descripcion_variante']
                            )
                        ) {

                            $datosVariante['descripcion'] =
                                $fila['descripcion_variante'];

                        }


                        if (
                            $idMarca !== null
                        ) {

                            $datosVariante['id_marca'] =
                                $idMarca;

                        }


                        $variante->update(
                            $datosVariante
                        );


                        $resultado['variantes_actualizadas']++;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Precio
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $fila['precio_estado']
                        === 'valido'
                        &&
                        $fila['precio_venta']
                        !== null
                    ) {

                        $precio =
                            PrecioProductoVariante::query()
                                ->where(
                                    'id_producto_variante',
                                    $variante
                                        ->id_producto_variante
                                )
                                ->first();


                        if (
                            !$precio
                        ) {

                            PrecioProductoVariante::create([

                                'id_producto_variante' =>
                                    $variante
                                        ->id_producto_variante,

                                /*
                                 * El Excel no contiene
                                 * estos datos.
                                 */
                                'costo_compra' =>
                                    null,

                                'precio_minimo' =>
                                    null,

                                'precio_venta' =>
                                    $fila['precio_venta'],

                                'fecha_vigencia' =>
                                    now(),

                                'observaciones' =>
                                    'Precio de venta importado desde el catálogo inicial.',

                                'estado_registro' =>
                                    'A',

                                'usuario_creacion' =>
                                    null,

                                'usuario_modificacion' =>
                                    null

                            ]);


                            $resultado['precios_creados']++;

                        } else {

                            $precioActual =
                                $precio->precio_venta
                                !== null
                                    ? (float)
                                $precio->precio_venta
                                    : null;


                            $precioNuevo =
                                (float)
                                $fila['precio_venta'];


                            if (
                                $precioActual === null
                                ||
                                abs(
                                    $precioActual
                                    - $precioNuevo
                                )
                                > 0.009
                            ) {

                                /*
                                 * No modificamos costo_compra
                                 * ni precio_minimo, ya que
                                 * podrían haber sido cargados
                                 * posteriormente desde el ERP.
                                 */
                                $precio->update([

                                    'precio_venta' =>
                                        $precioNuevo,

                                    'fecha_vigencia' =>
                                        now(),

                                    'estado_registro' =>
                                        'A'

                                ]);


                                $resultado['precios_actualizados']++;

                            } else {

                                $resultado['precios_sin_cambios']++;

                            }

                        }

                    } else {

                        /*
                         * No creamos precio 0.
                         * Tampoco borramos un precio
                         * previamente registrado.
                         */
                        $resultado['filas_sin_precio']++;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Revisión
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $fila['requiere_revision']
                    ) {

                        $resultado['filas_revision']++;

                    }


                    $resultado['filas_importadas']++;

                }


                return $resultado;

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Reporte CSV
    |--------------------------------------------------------------------------
    */

    public function generarReporte(
        array $analisis
    ): string
    {

        $directorio =
            storage_path(
                'app/importaciones'
            );


        File::ensureDirectoryExists(
            $directorio
        );


        $ruta =
            $directorio
            . DIRECTORY_SEPARATOR
            . 'catalogo_revision_'
            . now()
                ->format(
                    'Y-m-d_His'
                )
            . '.csv';


        $archivo =
            fopen(
                $ruta,
                'wb'
            );


        if (
            $archivo === false
        ) {

            throw new RuntimeException(
                'No se pudo crear el reporte CSV.'
            );

        }


        /*
         * BOM UTF-8 para Excel/LibreOffice.
         */
        fwrite(
            $archivo,
            "\xEF\xBB\xBF"
        );


        fputcsv(
            $archivo,
            [

                'fila_excel',
                'codigo_original',
                'codigo_comercial',
                'codigo_duplicado',
                'item_original',
                'item_normalizado',
                'categoria_slug',
                'controla_stock',
                'producto_propuesto',
                'variante_propuesta',
                'descripcion_variante',
                'marca_original',
                'marca_normalizada',
                'unidad_original',
                'unidad_medida',
                'precio_original',
                'precio_venta',
                'precio_estado',
                'confianza_clasificacion',
                'agrupacion_automatica',
                'requiere_revision',
                'bloquea_importacion',
                'omitir_importacion',
                'observaciones'
            ],
            ',',
            '"',
            ''
        );


        foreach (
            $analisis['filas']
            as $fila
        ) {

            fputcsv(
                $archivo,
                [

                    $fila['fila_excel'],

                    $fila['codigo_original'],

                    $fila['codigo_comercial'],

                    $fila['codigo_duplicado']
                        ? 'SI'
                        : 'NO',

                    $fila['item_original'],

                    $fila['item_normalizado'],

                    $fila['categoria_slug'],

                    $fila['controla_stock']
                        ? 'SI'
                        : 'NO',

                    $fila['producto_propuesto'],

                    $fila['variante_propuesta'],

                    $fila['descripcion_variante']
                    ?? null,

                    $fila['marca_original'],

                    $fila['marca_normalizada'],

                    $fila['unidad_original'],

                    $fila['unidad_medida'],

                    $fila['precio_original'],

                    $fila['precio_venta'],

                    $fila['precio_estado'],

                    $fila['confianza_clasificacion'],

                    $fila['agrupacion_automatica']
                        ? 'SI'
                        : 'NO',

                    $fila['requiere_revision']
                        ? 'SI'
                        : 'NO',

                    ($fila['bloquea_importacion'] ?? false)
                        ? 'SI'
                        : 'NO',

                    ($fila['omitir_importacion'] ?? false)
                        ? 'SI'
                        : 'NO',

                    implode(
                        ' | ',
                        $fila['observaciones']
                    )

                ],
                ',',
                '"',
                ''
            );

        }


        fclose(
            $archivo
        );


        return $ruta;

    }


    /*
    |--------------------------------------------------------------------------
    | Resumen
    |--------------------------------------------------------------------------
    */

    private function construirResumen(
        array $filas,
        array $duplicados
    ): array
    {

        $categorias =
            [];


        $productos =
            [];


        $preciosValidos =
            0;


        $sinPrecio =
            0;


        $preciosRevision =
            0;


        $marcasSinMarca =
            0;


        $marcasRevision =
            0;


        $unidadesAusentes =
            0;


        $requierenRevision =
            0;


        $sinCategoria =
            0;


        $agrupadas =
            0;


        $bloqueanImportacion =
            0;


        $omitidas =
            0;

        foreach (
            $filas
            as $fila
        ) {

            if (
                $fila['omitir_importacion']
                ?? false
            ) {

                $omitidas++;

                continue;

            }

            if (
                $fila['bloquea_importacion']
                ?? false
            ) {

                $bloqueanImportacion++;

            }

            $categoria =
                $fila['categoria_slug']
                ?? 'SIN_CLASIFICAR';


            $categorias[$categoria] =
                (
                    $categorias[$categoria]
                    ?? 0
                )
                + 1;


            if (
                $fila['categoria_slug']
                === null
            ) {

                $sinCategoria++;

            }


            $claveProducto =
                $categoria
                . '|'
                . $fila['producto_propuesto'];


            $productos[$claveProducto] =
                true;


            if (
                $fila['precio_estado']
                === 'valido'
            ) {

                $preciosValidos++;

            } elseif (
                $fila['precio_estado']
                === 'revisar'
            ) {

                $preciosRevision++;

            } else {

                $sinPrecio++;

            }


            if (
                $fila['marca_estado']
                === 'sin_marca'
            ) {

                $marcasSinMarca++;

            }


            if (
                $fila['marca_estado']
                === 'revisar'
            ) {

                $marcasRevision++;

            }


            if (
                $fila['unidad_medida']
                === null
            ) {

                $unidadesAusentes++;

            }


            if (
                $fila['requiere_revision']
            ) {

                $requierenRevision++;

            }


            if (
                $fila['agrupacion_automatica']
            ) {

                $agrupadas++;

            }

        }


        arsort(
            $categorias
        );


        return [

            'filas' =>
                count(
                    $filas
                ),

            'productos_propuestos' =>
                count(
                    $productos
                ),

            'variantes_propuestas' =>
                count(
                    $filas
                )
                - $omitidas,

            'filas_agrupadas' =>
                $agrupadas,

            'precios_validos' =>
                $preciosValidos,

            'sin_precio' =>
                $sinPrecio,

            'precios_revision' =>
                $preciosRevision,

            'marcas_sin_marca' =>
                $marcasSinMarca,

            'marcas_revision' =>
                $marcasRevision,

            'unidades_ausentes' =>
                $unidadesAusentes,

            'sin_categoria' =>
                $sinCategoria,

            'requieren_revision' =>
                $requierenRevision,

            'codigos_duplicados' =>
                count(
                    $duplicados
                ),

            'categorias' =>
                $categorias,

            'filas_omitidas' =>
                $omitidas,

            'bloquean_importacion' =>
                $bloqueanImportacion,

        ];

    }

    /*
|--------------------------------------------------------------------------
| Generar SKU
|--------------------------------------------------------------------------
*/

    private function generarSkuImportacion(
        array $fila
    ): string
    {

        /*
         * El SKU no depende del precio ni
         * de la clasificación propuesta.
         *
         * Si volvemos a importar el mismo
         * catálogo, genera el mismo SKU.
         */
        $componentes = [

            $fila['codigo_comercial']
            ?? '',

            $fila['item_normalizado']
            ?? '',

            $fila['unidad_medida']
            ?? '',

            $fila['marca_normalizada']
            ??
                $fila['marca_original']
                ??
                ''

        ];


        $componentes =
            array_map(
                function (
                    mixed $valor
                ) {

                    return mb_strtoupper(
                        trim(
                            (string)
                            $valor
                        ),
                        'UTF-8'
                    );

                },
                $componentes
            );


        $base =
            implode(
                '|',
                $componentes
            );


        return 'IRR-'
            . mb_strtoupper(
                substr(
                    hash(
                        'sha256',
                        $base
                    ),
                    0,
                    12
                ),
                'UTF-8'
            );

    }


    /*
    |--------------------------------------------------------------------------
    | Observación de importación
    |--------------------------------------------------------------------------
    */

    private function construirObservacionImportacion(
        array $fila
    ): string
    {

        $texto =
            'Importado desde catálogo inicial. '
            . 'Fila Excel: '
            . $fila['fila_excel']
            . '.';


        if (
            $fila['requiere_revision']
            &&
            count(
                $fila['observaciones']
            )
            > 0
        ) {

            $texto .=
                ' Revisión pendiente: '
                . implode(
                    ' | ',
                    $fila['observaciones']
                );

        }


        return $texto;

    }

    /*
    |--------------------------------------------------------------------------
    | Fila vacía
    |--------------------------------------------------------------------------
    */

    private function filaVacia(
        array $valores
    ): bool
    {

        foreach (
            $valores
            as $valor
        ) {

            if (
                $valor !== null
                &&
                trim(
                    (string)
                    $valor
                )
                !== ''
            ) {

                return false;

            }

        }


        return true;

    }

}
