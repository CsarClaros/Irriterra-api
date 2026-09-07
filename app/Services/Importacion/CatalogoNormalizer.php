<?php

namespace App\Services\Importacion;


class CatalogoNormalizer
{

    /*
    |--------------------------------------------------------------------------
    | Normalizar fila
    |--------------------------------------------------------------------------
    */

    public function normalizarFila(
        int   $filaExcel,
        mixed $codigo,
        mixed $item,
        mixed $precio,
        mixed $marca,
        mixed $unidad
    ): array
    {

        $observaciones = [];


        $codigoOriginal =
            $this->valorOriginal(
                $codigo
            );


        $itemOriginal =
            $this->valorOriginal(
                $item
            );


        $marcaOriginal =
            $this->valorOriginal(
                $marca
            );


        $unidadOriginal =
            $this->valorOriginal(
                $unidad
            );


        $codigoNormalizado =
            $this->normalizarCodigo(
                $codigo
            );


        $itemNormalizado =
            $this->normalizarTexto(
                $item
            );


        /*
        |--------------------------------------------------------------------------
        | Precio
        |--------------------------------------------------------------------------
        */

        $precioResultado =
            $this->normalizarPrecio(
                $precio
            );


        if (
            $precioResultado['observacion']
            !== null
        ) {

            $observaciones[] =
                $precioResultado['observacion'];

        }


        /*
        |--------------------------------------------------------------------------
        | Marca
        |--------------------------------------------------------------------------
        */

        $marcaResultado =
            $this->normalizarMarca(
                $marca
            );


        if (
            $marcaResultado['observacion']
            !== null
        ) {

            $observaciones[] =
                $marcaResultado['observacion'];

        }


        /*
        |--------------------------------------------------------------------------
        | Unidad
        |--------------------------------------------------------------------------
        */

        $unidadResultado =
            $this->normalizarUnidad(
                $unidad
            );

        /*
|--------------------------------------------------------------------------
| Inferir unidades ausentes
|--------------------------------------------------------------------------
*/

        if (
            $unidadResultado['valor'] === null
            &&
            $itemNormalizado !== null
        ) {

            $unidadInferida =
                $this->inferirUnidad(
                    $itemNormalizado
                );


            if (
                $unidadInferida !== null
            ) {

                $unidadResultado = [

                    'valor' =>
                        $unidadInferida,

                    'requiere_revision' =>
                        false,

                    'observacion' =>
                        'Unidad inferida automáticamente: '
                        . $unidadInferida

                ];

            }

        }


        if (
            $unidadResultado['observacion']
            !== null
        ) {

            $observaciones[] =
                $unidadResultado['observacion'];

        }


        /*
        |--------------------------------------------------------------------------
        | Validaciones mínimas
        |--------------------------------------------------------------------------
        */

        $requiereRevision =
            $precioResultado['requiere_revision']
            ||
            $marcaResultado['requiere_revision']
            ||
            $unidadResultado['requiere_revision'];


        if (
            $codigoNormalizado === null
        ) {

            $observaciones[] =
                'Código comercial ausente.';

            $requiereRevision =
                true;

        }


        if (
            $itemNormalizado === null
        ) {

            $observaciones[] =
                'Nombre de producto ausente.';

            $requiereRevision =
                true;

        }


        return [

            'fila_excel' =>
                $filaExcel,

            'codigo_original' =>
                $codigoOriginal,

            'codigo_comercial' =>
                $codigoNormalizado,

            'codigo_duplicado' =>
                false,

            'item_original' =>
                $itemOriginal,

            'item_normalizado' =>
                $itemNormalizado,

            'precio_original' =>
                $this->valorOriginal(
                    $precio
                ),

            'precio_venta' =>
                $precioResultado['valor'],

            'precio_estado' =>
                $precioResultado['estado'],

            'marca_original' =>
                $marcaOriginal,

            'marca_normalizada' =>
                $marcaResultado['valor'],

            'marca_estado' =>
                $marcaResultado['estado'],

            'unidad_original' =>
                $unidadOriginal,

            'unidad_medida' =>
                $unidadResultado['valor'],

            'requiere_revision' =>
                $requiereRevision,

            'observaciones' =>
                $observaciones

        ];

    }


    /*
    |--------------------------------------------------------------------------
    | Valor original
    |--------------------------------------------------------------------------
    */

    private function valorOriginal(
        mixed $valor
    ): ?string
    {

        if (
            $valor === null
        ) {

            return null;

        }


        $valor =
            trim(
                (string)
                $valor
            );


        return $valor !== ''
            ? $valor
            : null;

    }


    /*
    |--------------------------------------------------------------------------
    | Texto
    |--------------------------------------------------------------------------
    */

    public function normalizarTexto(
        mixed $valor
    ): ?string
    {

        if (
            $valor === null
        ) {

            return null;

        }


        $texto =
            (string)
            $valor;


        /*
         * Caracteres encontrados en
         * diferentes fuentes del catálogo.
         */
        $texto =
            strtr(
                $texto,
                [

                    "\u{00A0}" =>
                        ' ',

                    'х' =>
                        'x',

                    'Х' =>
                        'X',

                    '×' =>
                        'x',

                    'З' =>
                        '3',

                    'з' =>
                        '3',

                    '“' =>
                        '"',

                    '”' =>
                        '"',

                    '″' =>
                        '"',

                    '’' =>
                        "'",

                    '–' =>
                        '-',

                    '—' =>
                        '-'

                ]
            );


        $texto =
            preg_replace(
                '/\s+/u',
                ' ',
                $texto
            );


        $texto =
            trim(
                $texto ?? ''
            );


        return $texto !== ''
            ? $texto
            : null;

    }


    /*
    |--------------------------------------------------------------------------
    | Código
    |--------------------------------------------------------------------------
    */

    private function normalizarCodigo(
        mixed $valor
    ): ?string
    {

        $codigo =
            $this->normalizarTexto(
                $valor
            );


        if (
            $codigo === null
        ) {

            return null;

        }


        return mb_strtoupper(
            $codigo,
            'UTF-8'
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Precio
    |--------------------------------------------------------------------------
    */

    private function normalizarPrecio(
        mixed $valor
    ): array
    {

        if (
            $valor === null
            ||
            trim(
                (string)
                $valor
            ) === ''
        ) {

            return [

                'valor' =>
                    null,

                'estado' =>
                    'sin_precio',

                'requiere_revision' =>
                    false,

                'observacion' =>
                    null

            ];

        }


        /*
        |--------------------------------------------------------------------------
        | Valor numérico
        |--------------------------------------------------------------------------
        */

        if (
            is_int(
                $valor
            )
            ||
            is_float(
                $valor
            )
        ) {

            $numero =
                (float)
                $valor;


            if (
                $numero <= 0
            ) {

                return [

                    'valor' =>
                        null,

                    'estado' =>
                        'sin_precio',

                    'requiere_revision' =>
                        false,

                    'observacion' =>
                        'Precio cero tratado como precio no disponible.'

                ];

            }


            return [

                'valor' =>
                    round(
                        $numero,
                        2
                    ),

                'estado' =>
                    'valido',

                'requiere_revision' =>
                    false,

                'observacion' =>
                    null

            ];

        }


        /*
        |--------------------------------------------------------------------------
        | Texto
        |--------------------------------------------------------------------------
        */

        $texto =
            trim(
                (string)
                $valor
            );


        $textoNumerico =
            str_replace(
                ',',
                '.',
                $texto
            );


        if (
            is_numeric(
                $textoNumerico
            )
        ) {

            $numero =
                (float)
                $textoNumerico;


            return [

                'valor' =>
                    $numero > 0
                        ? round(
                        $numero,
                        2
                    )
                        : null,

                'estado' =>
                    $numero > 0
                        ? 'valido'
                        : 'sin_precio',

                'requiere_revision' =>
                    false,

                'observacion' =>
                    null

            ];

        }


        /*
         * Ejemplos de valores que pueden
         * representar dos precios:
         *
         * 350 .- 280
         * 4 .- 6
         */
        return [

            'valor' =>
                null,

            'estado' =>
                'revisar',

            'requiere_revision' =>
                true,

            'observacion' =>
                'Precio ambiguo: '
                . $texto

        ];

    }


    /*
    |--------------------------------------------------------------------------
    | Unidad
    |--------------------------------------------------------------------------
    */

    private function normalizarUnidad(
        mixed $valor
    ): array
    {

        $unidad =
            $this->normalizarTexto(
                $valor
            );


        if (
            $unidad === null
        ) {

            return [

                'valor' =>
                    null,

                'requiere_revision' =>
                    true,

                'observacion' =>
                    'Unidad de medida ausente.'

            ];

        }


        $unidad =
            mb_strtoupper(
                $unidad,
                'UTF-8'
            );


        $mapa = [

            'PZAS' =>
                'PZA',

            'MT2' =>
                'M2'

        ];


        $unidad =
            $mapa[$unidad]
            ?? $unidad;


        $unidadesValidas = [

            'PZA',

            'METRO',

            'EQUIPO',

            'BARRA',

            'M2',

            'GLOBAL',

            'ROLLO',

            'KG'

        ];


        if (
            !in_array(
                $unidad,
                $unidadesValidas,
                true
            )
        ) {

            return [

                'valor' =>
                    $unidad,

                'requiere_revision' =>
                    true,

                'observacion' =>
                    'Unidad de medida no reconocida: '
                    . $unidad

            ];

        }


        return [

            'valor' =>
                $unidad,

            'requiere_revision' =>
                false,

            'observacion' =>
                null

        ];

    }


    /*
    |--------------------------------------------------------------------------
    | Marca
    |--------------------------------------------------------------------------
    */

    private function normalizarMarca(
        mixed $valor
    ): array
    {

        $marca =
            $this->normalizarTexto(
                $valor
            );


        if (
            $marca === null
        ) {

            return [

                'valor' =>
                    null,

                'estado' =>
                    'sin_marca',

                'requiere_revision' =>
                    false,

                'observacion' =>
                    null

            ];

        }


        $marca =
            mb_strtoupper(
                $marca,
                'UTF-8'
            );


        /*
        |--------------------------------------------------------------------------
        | Variaciones conocidas
        |--------------------------------------------------------------------------
        */

        $mapa = [

            'IRRITIME / TURQUIA' =>
                'IRRITIME',

            'IRRITIME/TURQUIA' =>
                'IRRITIME',

            'POELSAN / TURQUIA' =>
                'POELSAN',

            'POELSAN/ TURQUIA' =>
                'POELSAN',

            'PAVCO/COLOMBIA' =>
                'PAVCO',

            'SUNSTREAM/TURQUIA' =>
                'SUNSTREAM',

            'CAMPEON / BOLIVIA' =>
                'CAMPEON',

            'AZUD / ESPAÑA' =>
                'AZUD',

            'ABRISA/ESPAÑA' =>
                'ABRISA',

            'STF / TURQUIA' =>
                'STF',

            'CITY PUMS / ITALIA' =>
                'CITY PUMS'

        ];


        if (
            isset(
                $mapa[$marca]
            )
        ) {

            return [

                'valor' =>
                    $mapa[$marca],

                'estado' =>
                    'normalizada',

                'requiere_revision' =>
                    false,

                'observacion' =>
                    null

            ];

        }


        /*
        |--------------------------------------------------------------------------
        | Valores ambiguos
        |--------------------------------------------------------------------------
        */

        $ambiguas = [

            'KRONA - TIGRE',

            'TIGRE - KRONA',

            'GR - STF / TURQUIA',

            'ABRISA - GREEN PLAINS',

            'TURQUIA'

        ];


        if (
            in_array(
                $marca,
                $ambiguas,
                true
            )
        ) {

            return [

                'valor' =>
                    null,

                'estado' =>
                    'revisar',

                'requiere_revision' =>
                    true,

                'observacion' =>
                    'Marca ambigua: '
                    . $marca

            ];

        }


        /*
        |--------------------------------------------------------------------------
        | Marcas estructuradas actualmente
        |--------------------------------------------------------------------------
        */

        $marcasConocidas = [

            'ABRISA',
            'ALCON',
            'AZUD',
            'BONHOFER',
            'BRIGS STRATTON',
            'CAMPEON',
            'CAMSCO',
            'CITY PUMS',
            'DAEWOO',
            'GOLDEN SPRAY',
            'GR',
            'GREEN PLAINS',
            'HONDA',
            'IRRITIME',
            'KHOLER',
            'KRONA',
            'LT',
            'MULLER',
            'NAANDAN JAIN',
            'NETAFIM',
            'NUEVA ERA',
            'PAVCO',
            'PLASSON',
            'POELSAN',
            'PQA',
            'SANKING',
            'SECTORIAL JOLLY',
            'SENNINGER',
            'STF',
            'SUNSTREAM',
            'TERMOPLAST',
            'TIGRE',
            'TUPY',
            'UNIRAIN'

        ];


        if (
            in_array(
                $marca,
                $marcasConocidas,
                true
            )
        ) {

            return [

                'valor' =>
                    $marca,

                'estado' =>
                    'valida',

                'requiere_revision' =>
                    false,

                'observacion' =>
                    null

            ];

        }


        return [

            'valor' =>
                $marca,

            'estado' =>
                'revisar',

            'requiere_revision' =>
                true,

            'observacion' =>
                'Marca no reconocida en el catálogo de marcas: '
                . $marca

        ];

    }

    /*
|--------------------------------------------------------------------------
| Inferir unidad
|--------------------------------------------------------------------------
*/

    private function inferirUnidad(
        string $item
    ): ?string
    {

        $item =
            mb_strtoupper(
                $item,
                'UTF-8'
            );


        /*
         * Las mangueras de succión
         * se comercializan por metro.
         */
        if (
            str_contains(
                $item,
                'MANGUERA DE SUCCION'
            )
        ) {

            return 'METRO';

        }

        /*
|--------------------------------------------------------------------------
| Equipos completos
|--------------------------------------------------------------------------
*/

        $equipos = [

            'ELECTROBOMBA ',

            'MOTOBOMBA ',

            'MOTOBOMBA DE ',

            'TANQUE AUSTRALIANO'

        ];


        foreach (
            $equipos
            as $prefijo
        ) {

            if (
                str_starts_with(
                    $item,
                    $prefijo
                )
            ) {

                return 'EQUIPO';

            }

        }


        /*
         * Accesorios discretos.
         */
        $prefijosPieza = [

            'CAMLOCK ',

            'CODO 45° PVC',

            'CODO 90° PVC',

            'CODO ROSCA HEMBRA',

            'CODO SIMPLE DENTADO',

            'NIPLE PVC',

            'TAPON TIPO 8'

        ];


        foreach (
            $prefijosPieza
            as $prefijo
        ) {

            if (
                str_starts_with(
                    $item,
                    $prefijo
                )
            ) {

                return 'PZA';

            }

        }


        return null;

    }

}
