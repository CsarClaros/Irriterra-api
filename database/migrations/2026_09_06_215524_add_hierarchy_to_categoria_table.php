<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    /**
     * Ejecuta la migración.
     */
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Estructura jerárquica
        |--------------------------------------------------------------------------
        */

        Schema::table(
            'categoria',
            function (Blueprint $table) {

                /*
                 * El nombre deja de ser
                 * globalmente único.
                 */
                $table->dropUnique(
                    'categoria_nombre_unique'
                );


                /*
                 * Categoría padre.
                 */
                $table
                    ->unsignedBigInteger(
                        'id_categoria_padre'
                    )
                    ->nullable()
                    ->after(
                        'id_categoria'
                    );


                /*
                 * Identificador amigable.
                 */
                $table
                    ->string(
                        'slug',
                        180
                    )
                    ->nullable()
                    ->after(
                        'nombre'
                    );


                /*
                 * Orden de presentación.
                 */
                $table
                    ->unsignedInteger(
                        'orden'
                    )
                    ->default(
                        0
                    )
                    ->after(
                        'descripcion'
                    );


                /*
                 * Relación autorreferenciada.
                 */
                $table
                    ->foreign(
                        'id_categoria_padre',
                        'categoria_padre_fk'
                    )
                    ->references(
                        'id_categoria'
                    )
                    ->on(
                        'categoria'
                    )
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();


                /*
                 * Optimización de consultas
                 * jerárquicas.
                 */
                $table
                    ->index(
                        [
                            'id_categoria_padre',
                            'orden'
                        ],
                        'categoria_padre_orden_index'
                    );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Slugs de categorías existentes
        |--------------------------------------------------------------------------
        */

        $categorias =
            DB::table(
                'categoria'
            )
                ->select([
                    'id_categoria',
                    'nombre'
                ])
                ->orderBy(
                    'id_categoria'
                )
                ->get();


        $slugsUsados = [];


        foreach (
            $categorias
            as $categoria
        ) {

            $base =
                Str::slug(
                    $categoria->nombre
                );


            if (
                $base === ''
            ) {

                $base =
                    'categoria-'
                    . $categoria->id_categoria;

            }


            $slug =
                $base;

            $numero =
                2;


            while (
            isset(
                $slugsUsados[$slug]
            )
            ) {

                $slug =
                    $base
                    . '-'
                    . $numero;

                $numero++;

            }


            DB::table(
                'categoria'
            )
                ->where(
                    'id_categoria',
                    $categoria->id_categoria
                )
                ->update([
                    'slug' =>
                        $slug
                ]);


            $slugsUsados[$slug] = true;
        }


        /*
        |--------------------------------------------------------------------------
        | Slug obligatorio y único
        |--------------------------------------------------------------------------
        */

        Schema::table(
            'categoria',
            function (Blueprint $table) {

                $table
                    ->string(
                        'slug',
                        180
                    )
                    ->nullable(
                        false
                    )
                    ->change();


                $table
                    ->unique(
                        'slug',
                        'categoria_slug_unique'
                    );
            }
        );
    }


    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::table(
            'categoria',
            function (Blueprint $table) {

                $table->dropForeign(
                    'categoria_padre_fk'
                );


                $table->dropIndex(
                    'categoria_padre_orden_index'
                );


                $table->dropUnique(
                    'categoria_slug_unique'
                );


                $table->dropColumn([
                    'id_categoria_padre',
                    'slug',
                    'orden'
                ]);


                $table->unique(
                    'nombre',
                    'categoria_nombre_unique'
                );
            }
        );
    }
};
