<?php

namespace App\Services\Importacion;


class CatalogoClassifier
{

    /*
    |--------------------------------------------------------------------------
    | Clasificar
    |--------------------------------------------------------------------------
    */

    public function clasificar(
        array $fila
    ): array
    {

        $item =
            mb_strtoupper(
                $fila['item_normalizado']
                ?? '',
                'UTF-8'
            );


        $marca =
            $fila['marca_normalizada']
            ?? null;


        $categoria =
            $this->clasificarCategoria(
                $item,
                $marca
            );


        $familia =
            $this->clasificarFamilia(
                $item
            );


        $requiereRevision =
            $fila['requiere_revision']
            ||
            $categoria['requiere_revision'];


        $observaciones =
            $fila['observaciones'];


        if (
            $categoria['observacion']
            !== null
        ) {

            $observaciones[] =
                $categoria['observacion'];

        }


        return array_merge(
            $fila,
            [

                'categoria_slug' =>
                    $categoria['slug'],

                'producto_propuesto' =>
                    $familia['producto'],

                'variante_propuesta' =>
                    $familia['variante'],

                'descripcion_variante' =>
                    $familia['descripcion_variante']
                    ?? null,

                'controla_stock' =>
                    $categoria['slug']
                    !== 'servicios',

                'agrupacion_automatica' =>
                    $familia['agrupado'],

                'confianza_clasificacion' =>
                    $categoria['confianza'],

                'requiere_revision' =>
                    $requiereRevision,

                'observaciones' =>
                    $observaciones,

                'omitir_importacion' =>
                    false,

                'bloquea_importacion' =>
                    (
                        $categoria['slug'] === null
                        ||
                        $fila['unidad_medida'] === null
                        ||
                        $fila['item_normalizado'] === null
                    ),

            ]
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Categoría
    |--------------------------------------------------------------------------
    */

    private function clasificarCategoria(
        string  $item,
        ?string $marca
    ): array
    {

        /*
        |--------------------------------------------------------------------------
        | Geomembranas
        |--------------------------------------------------------------------------
        */

        if (
            str_contains(
                $item,
                'GEOMEMBRANA'
            )
        ) {

            return $this->categoria(
                'geomembranas'
            );

        }


        /*
|--------------------------------------------------------------------------
| Servicios
|--------------------------------------------------------------------------
*/

        if (
            str_starts_with(
                $item,
                'INSTALACION '
            )
            ||
            str_starts_with(
                $item,
                'TRANSPORTE '
            )
        ) {

            return $this->categoria(
                'servicios'
            );

        }
        /*
        |--------------------------------------------------------------------------
        | Maquinaria
        |--------------------------------------------------------------------------
        */

        if (
            str_contains(
                $item,
                'MOTOCULTOR'
            )
            ||
            str_contains(
                $item,
                'ABRIDOR DE SURCOS'
            )
            ||
            str_starts_with(
                $item,
                'ARADO '
            )
        ) {

            return $this->categoria(
                'maquinaria-motocultivadores'
            );

        }


        if (
            str_starts_with(
                $item,
                'MOTOR '
            )
        ) {

            return $this->categoria(
                'maquinaria-motores'
            );

        }


        if (
            str_contains(
                $item,
                'MOTOBOMBA'
            )
            &&
            !str_contains(
                $item,
                'CANASTILLO'
            )
            &&
            !str_contains(
                $item,
                'TERMINAL PARA'
            )
        ) {

            return $this->categoria(
                'maquinaria-motobombas'
            );

        }


        if (
            str_starts_with(
                $item,
                'BOMBA CENTRIFUGA'
            )
            ||
            str_starts_with(
                $item,
                'ELECTROBOMBA'
            )
        ) {

            return $this->categoria(
                'maquinaria-bombas-electricas'
            );

        }

        if (
            str_starts_with(
                $item,
                'GENERADOR '
            )
        ) {

            return $this->categoria(
                'maquinaria-generadores'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Tuberías
        |--------------------------------------------------------------------------
        */

        if (
            str_starts_with(
                $item,
                'TUBERIA HDPE'
            )
        ) {

            return $this->categoria(
                'riego-tuberias-hdpe'
            );

        }


        if (
            str_starts_with(
                $item,
                'TUBERIA PVC'
            )
        ) {

            return $this->categoria(
                'riego-tuberias-pvc'
            );

        }


        if (
            str_starts_with(
                $item,
                'POLITUBO'
            )
            ||
            str_starts_with(
                $item,
                'POLIETILENO INTEGRADO'
            )
        ) {

            return $this->categoria(
                'riego-tuberias-politubos'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Mangueras
        |--------------------------------------------------------------------------
        */

        if (
            str_contains(
                $item,
                'MANGUERA DE SUCCION'
            )
        ) {

            return $this->categoria(
                'riego-mangueras-succion'
            );

        }


        if (
            str_starts_with(
                $item,
                'MANGUERA CIEGA'
            )
        ) {

            return $this->categoria(
                'riego-mangueras-ciegas'
            );

        }


        if (
            str_starts_with(
                $item,
                'MANGUERA '
            )
        ) {

            return $this->categoria(
                'riego-mangueras',
                'media'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Cintas
        |--------------------------------------------------------------------------
        */

        if (
            str_contains(
                $item,
                'CINTA DE GOTEO'
            )
            ||
            str_starts_with(
                $item,
                'CINTA DE RIEGO'
            )
        ) {

            return $this->categoria(
                'riego-cintas-goteo'
            );

        }


        if (
            str_starts_with(
                $item,
                'CINTA DE LLUVIA'
            )
        ) {

            return $this->categoria(
                'riego-cintas-lluvia'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Aspersión
        |--------------------------------------------------------------------------
        */

        if (
            str_contains(
                $item,
                'ASPERSOR'
            )
            ||
            str_contains(
                $item,
                'PORTA ASPERSOR'
            )
        ) {

            return $this->categoria(
                'riego-aspersores'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Filtros
        |--------------------------------------------------------------------------
        */

        if (
            str_starts_with(
                $item,
                'FILTRO '
            )
            ||
            str_starts_with(
                $item,
                'MINI FILTRO'
            )
        ) {

            return $this->categoria(
                'riego-filtros'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Válvulas
        |--------------------------------------------------------------------------
        */

        if (
            str_starts_with(
                $item,
                'VALVULA '
            )
            ||
            str_starts_with(
                $item,
                'LLAVE DE PASO'
            )
        ) {

            return $this->categoria(
                'riego-valvulas'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Fitting HDPE
        |--------------------------------------------------------------------------
        */

        $fittingHdpe = [

            'COLLARIN DE TOMA',
            'CODO ROSCA HEMBRA',
            'ENLACE CODO',
            'ENLACE REDUCIDO',
            'ENLACE RECTO',
            'ENLACE ROSCA HEMBRA',
            'ENLACE ROSCA MACHO',
            'ENLACE TAPON',
            'TE BOCAS IGUALES',
            'TE ROSCA HEMBRA'

        ];


        foreach (
            $fittingHdpe
            as $prefijo
        ) {

            if (
                str_starts_with(
                    $item,
                    $prefijo
                )
            ) {

                return $this->categoria(
                    'riego-accesorios-fitting-hdpe'
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | PVC
        |--------------------------------------------------------------------------
        */

        if (
            str_contains(
                $item,
                'PVC'
            )
        ) {

            return $this->categoria(
                'riego-accesorios-pvc'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Mini accesorios
        |--------------------------------------------------------------------------
        */

        $miniAccesorios = [

            'GOTERO ',
            'CONECTOR ',
            'MINIVALVULA ',
            'PUNZON ',
            'FINAL CINTA',
            'ESTACA ',
            'GANCHO ',
            'TAPON SUELTO',
            'BROCA MANUAL'

        ];


        foreach (
            $miniAccesorios
            as $prefijo
        ) {

            if (
                str_starts_with(
                    $item,
                    $prefijo
                )
            ) {

                return $this->categoria(
                    'riego-accesorios-mini'
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Accesorios generales
        |--------------------------------------------------------------------------
        */

        if (
            str_starts_with(
                $item,
                'CAMLOCK '
            )
            ||
            str_contains(
                $item,
                'TERMINAL PARA MOTOBOMBA'
            )
            ||
            str_contains(
                $item,
                'CANASTILLO SUCCION MOTOBOMBA'
            )
        ) {

            return $this->categoria(
                'riego-accesorios',
                'media'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Tanques
        |--------------------------------------------------------------------------
        */

        if (
            str_starts_with(
                $item,
                'TANQUE '
            )
        ) {

            return $this->categoria(
                'riego-tanques'
            );

        }

        /*
|--------------------------------------------------------------------------
| Aspersión adicional
|--------------------------------------------------------------------------
*/

        $aspersoresAdicionales = [

            'MICROJET ',

            'NEBULIZADOR ',

            'TRIPODE ',

            'KIT AGROESTAND'

        ];


        foreach (
            $aspersoresAdicionales
            as $prefijo
        ) {

            if (
                str_starts_with(
                    $item,
                    $prefijo
                )
            ) {

                return $this->categoria(
                    'riego-aspersores'
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Filtros adicionales
        |--------------------------------------------------------------------------
        */

        if (
            str_starts_with(
                $item,
                'HIDROCICLON '
            )
        ) {

            return $this->categoria(
                'riego-filtros'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Válvulas adicionales
        |--------------------------------------------------------------------------
        */

        if (
            str_starts_with(
                $item,
                'FLOTADOR '
            )
        ) {

            return $this->categoria(
                'riego-valvulas'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Mini accesorios adicionales
        |--------------------------------------------------------------------------
        */

        $miniAdicionales = [

            'ACOPLE DE ',

            'CODO SIMPLE DENTADO',

            'EMPAQUE INICIAL',

            'ENLACE SIMPLE',

            'ENLACE MIXTO ESPIGA',

            'FINAL DE LINEA',

            'JUNTA BILABIAL',

            'NIPLE BARBADO',

            'NIPLE MACHO BARBADO',

            'PIQUET LABERINTO',

            'TAPON FINAL TIPO',

            'TAPON PARA PERFORACION',

            'TAPON TIPO 8',

            'TE BARBADA',

            'TE SIMPLE DENTADO',

            'YEE SIMPLE DENTADO',

            'MICROTUBO '

        ];


        foreach (
            $miniAdicionales
            as $prefijo
        ) {

            if (
                str_starts_with(
                    $item,
                    $prefijo
                )
            ) {

                return $this->categoria(
                    'riego-accesorios-mini'
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Accesorios generales adicionales
        |--------------------------------------------------------------------------
        */

        $accesoriosGenerales = [

            'ABRAZADERA ',

            'ADAPTADOR HEMBRA',

            'BROCA PALETA',

            'BUJE REDUCCION FG',

            'CODO FG',

            'CUPLA FG',

            'ELEVADOR DE FIERRO',

            'ENLACE TIPO CAMPANA',

            'INYECTOR VENTURY',

            'LLAVE PARA CONECTORES',

            'LLAVE INICIAL PARA CINTA DE LLUVIA',

            'MANOMETRO ',

            'NIPLE REDUCCION ALUMINIO',

            'PITON TERMINAL',

            'SPANER ',

            'TEFLON',

            'TEMPORIZADOR ELECTRONICO',

            'TERMINAL PARA CINTA DE LLUVIA',

            'UNION UNIVERSAL',

            'VENTURI ',

            'Y DE '

        ];


        foreach (
            $accesoriosGenerales
            as $prefijo
        ) {

            if (
                str_starts_with(
                    $item,
                    $prefijo
                )
            ) {

                return $this->categoria(
                    'riego-accesorios',
                    'media'
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Insumos y accesorios generales
        |--------------------------------------------------------------------------
        */

        $accesoriosComplementarios = [

            'ALAMBRE DE AMARRE',

            'CAJA METALICA',

            'EMBUDO GALVANIZADO',

            'MALLA MILIMETRICA'

        ];


        foreach (
            $accesoriosComplementarios
            as $prefijo
        ) {

            if (
                str_starts_with(
                    $item,
                    $prefijo
                )
            ) {

                return $this->categoria(
                    'riego-accesorios',
                    'media'
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Sin clasificar
        |--------------------------------------------------------------------------
        */

        return $this->categoria(
            null,
            'baja',
            true,
            'No se pudo determinar automáticamente una categoría.'
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Familia Producto / Variante
    |--------------------------------------------------------------------------
    */

    private function clasificarFamilia(
        string $item
    ): array
    {

        /*
        |--------------------------------------------------------------------------
        | Tubería HDPE
        |--------------------------------------------------------------------------
        */

        if (
            preg_match(
                '/^TUBERIA HDPE AGRO PN(6|8|10)\b(.*)$/u',
                $item,
                $match
            )
        ) {

            return $this->familia(
                'Tubería HDPE Agro PN'
                . $match[1],
                $match[2]
            );

        }


        if (
            preg_match(
                '/^TUBERIA HDPE PN(6|8|10|16)\b(.*)$/u',
                $item,
                $match
            )
        ) {

            return $this->familia(
                'Tubería HDPE PN'
                . $match[1],
                $match[2]
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Tubería PVC
        |--------------------------------------------------------------------------
        */

        if (
            preg_match(
                '/^TUBERIA PVC E-40\b(.*)$/u',
                $item,
                $match
            )
        ) {

            return $this->familia(
                'Tubería PVC E-40',
                $match[1]
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Politubo
        |--------------------------------------------------------------------------
        */

        if (
            preg_match(
                '/^POLITUBO BICAPA\s*-\s*PN(04|06|08|10)\b(.*)$/u',
                $item,
                $match
            )
        ) {

            return $this->familia(
                'Politubo bicapa PN'
                . $match[1],
                $match[2]
            );

        }


        if (
            preg_match(
                '/^POLITUBO BICAPA\b(.*)$/u',
                $item,
                $match
            )
        ) {

            return $this->familia(
                'Politubo bicapa',
                $match[1]
            );

        }


        if (
            preg_match(
                '/^POLITUBO TRICAPA\b(.*)$/u',
                $item,
                $match
            )
        ) {

            return $this->familia(
                'Politubo tricapa',
                $match[1]
            );

        }


        if (
            preg_match(
                '/^POLITUBO\s*-\s*PN(06|08|10)\b(.*)$/u',
                $item,
                $match
            )
        ) {

            return $this->familia(
                'Politubo PN'
                . $match[1],
                $match[2]
            );

        }


        /*
        |--------------------------------------------------------------------------
        | PVC repetitivo
        |--------------------------------------------------------------------------
        */

        $familiasFijas = [

            'BRIDA PVC E-40 - RH-M' =>
                'Brida PVC E-40 RH-M',

            'BUJE REDUCCION PVC E-40 - RM-H' =>
                'Buje reducción PVC E-40 RM-H',

            'CODO 45° PVC E-40 - RH-H' =>
                'Codo 45° PVC E-40 RH-H',

            'CODO 90° PVC E-40 - RH-H' =>
                'Codo 90° PVC E-40 RH-H',

            'CUPLA PVC E-40 - RH-H' =>
                'Cupla PVC E-40 RH-H',

            'NIPLE PVC E-40 - RM-M' =>
                'Niple PVC E-40 RM-M',

            'TAPON HEMBRA PVC E-40 - RH' =>
                'Tapón hembra PVC E-40 RH',

            'TAPON MACHO PVC E-40 - RM' =>
                'Tapón macho PVC E-40 RM',

            'TE PVC E-40 - RH-H-H' =>
                'Te PVC E-40 RH-H-H',

            'UNION UNIVERSAL PVC E-40' =>
                'Unión universal PVC E-40',

            'LLAVE DE PASO CON UNION UNIVERSAL PVC E-40' =>
                'Llave de paso con unión universal PVC E-40',

            'LLAVE DE PASO PVC E-40' =>
                'Llave de paso PVC E-40',

            'VALVULA P/POZO PVC E-40 - RH' =>
                'Válvula para pozo PVC E-40 RH'

        ];


        foreach (
            $familiasFijas
            as $prefijo => $producto
        ) {

            $itemCompacto =
                preg_replace(
                    '/\s+/u',
                    ' ',
                    $item
                );


            if (
                str_starts_with(
                    $itemCompacto,
                    $prefijo
                )
            ) {

                return $this->familia(
                    $producto,
                    mb_substr(
                        $itemCompacto,
                        mb_strlen(
                            $prefijo
                        )
                    )
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Fitting HDPE
        |--------------------------------------------------------------------------
        */

        $familiasHdpe = [

            'COLLARIN DE TOMA PN16' =>
                'Collarín de toma PN16',

            'CODO ROSCA HEMBRA PN16' =>
                'Codo rosca hembra PN16',

            'ENLACE REDUCIDO PN16' =>
                'Enlace reducido PN16',

            'ENLACE RECTO PN16' =>
                'Enlace recto PN16',

            'ENLACE ROSCA HEMBRA PN16' =>
                'Enlace rosca hembra PN16',

            'ENLACE ROSCA MACHO PN 16' =>
                'Enlace rosca macho PN16',

            'ENLACE TAPON PN16' =>
                'Enlace tapón PN16',

            'TE BOCAS IGUALES PN16' =>
                'Te bocas iguales PN16',

            'TE ROSCA HEMBRA PN16' =>
                'Te rosca hembra PN16'

        ];


        foreach (
            $familiasHdpe
            as $prefijo => $producto
        ) {

            if (
                str_starts_with(
                    $item,
                    $prefijo
                )
            ) {

                return $this->familia(
                    $producto,
                    mb_substr(
                        $item,
                        mb_strlen(
                            $prefijo
                        )
                    )
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Otros grupos claros
        |--------------------------------------------------------------------------
        */

        $grupos = [

            'TANQUE DE ALMACENAMIENTO' =>
                'Tanque de almacenamiento',

            'CINTA DE GOTEO' =>
                'Cinta de goteo',

            'CINTA DE RIEGO' =>
                'Cinta de riego',

            'CINTA DE LLUVIA' =>
                'Cinta de lluvia',

            'MANGUERA CIEGA' =>
                'Manguera ciega',

            'MANGUERA DE SUCCION' =>
                'Manguera de succión',

            'MANGUERA CON GOTERO STAR' =>
                'Manguera con gotero Star',

            'FILTRO DE ANILLAS' =>
                'Filtro de anillas',

            'FILTRO DE MALLAS' =>
                'Filtro de mallas',

            'MINI FILTRO' =>
                'Mini filtro',

            'GOTERO ELF AUTOCOMPENSADO' =>
                'Gotero ELF autocompensado',

            'GOTERO REGULABLE' =>
                'Gotero regulable',

            'ASPERSOR CIRCULAR' =>
                'Aspersor circular',

            'PORTA ASPERSOR CENTRAL DE ALUMINIO' =>
                'Porta aspersor central de aluminio',

            'MOTOCULTOR' =>
                'Motocultor',

            'MOTOBOMBA' =>
                'Motobomba',

            'GEOMEMBRANA NOMINAL PQA' =>
                'Geomembrana nominal PQA',

            'GEOMEMBRANA GM13 PQA' =>
                'Geomembrana GM13 PQA'

        ];


        foreach (
            $grupos
            as $prefijo => $producto
        ) {

            if (
                str_starts_with(
                    $item,
                    $prefijo
                )
            ) {

                return $this->familia(
                    $producto,
                    mb_substr(
                        $item,
                        mb_strlen(
                            $prefijo
                        )
                    )
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Camlock
        |--------------------------------------------------------------------------
        */

        if (
            preg_match(
                '/^CAMLOCK ALUMINIO TIPO ([A-F])\b(.*)$/u',
                $item,
                $match
            )
        ) {

            return $this->familia(
                'Camlock aluminio tipo '
                . $match[1],
                $match[2]
            );

        }

        /*
|--------------------------------------------------------------------------
| Servicios
|--------------------------------------------------------------------------
*/

        if (
            preg_match(
                '/^INSTALACION\b(.*)$/u',
                $item,
                $match
            )
        ) {

            return $this->familia(
                'Instalación',
                $match[1]
            );

        }


        if (
            preg_match(
                '/^TRANSPORTE\b(.*)$/u',
                $item,
                $match
            )
        ) {

            return $this->familia(
                'Transporte',
                $match[1]
            );

        }

        /*
|--------------------------------------------------------------------------
| Cabezal completo
|--------------------------------------------------------------------------
*/

        if (
            preg_match(
                '/^CABEZAL COMPLETO\s+([^()]+)\s*\((.*)\)$/u',
                $item,
                $match
            )
        ) {

            return [

                'producto' =>
                    'Cabezal completo',

                'variante' =>
                    trim(
                        $match[1]
                    ),

                'agrupado' =>
                    true,

                'descripcion_variante' =>
                    trim(
                        $match[2]
                    )

            ];

        }


        /*
        |--------------------------------------------------------------------------
        | Producto individual
        |--------------------------------------------------------------------------
        |
        | No necesariamente es un error.
        | Puede ser un producto que solo
        | posee una variante.
        |
        */

        return [

            'producto' =>
                $item,

            'variante' =>
                'Estándar',

            'agrupado' =>
                false

        ];

    }


    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function categoria(
        ?string $slug,
        string  $confianza = 'alta',
        bool    $requiereRevision = false,
        ?string $observacion = null
    ): array
    {

        return [

            'slug' =>
                $slug,

            'confianza' =>
                $confianza,

            'requiere_revision' =>
                $requiereRevision,

            'observacion' =>
                $observacion

        ];

    }


    private function familia(
        string $producto,
        string $variante
    ): array
    {

        $variante =
            trim(
                $variante,
                " \t\n\r\0\x0B-,"
            );


        if (
            $variante === ''
        ) {

            $variante =
                'Estándar';

        }


        return [

            'producto' =>
                $producto,

            'variante' =>
                $variante,

            'agrupado' =>
                true

        ];

    }

}
