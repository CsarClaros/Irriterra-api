<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::table(
            'sucursal',
            function (
                Blueprint $table
            ): void {

                /*
                |--------------------------------------------------------------------------
                | URL para mapa incrustado
                |--------------------------------------------------------------------------
                */

                $table
                    ->text(
                        'url_maps_embed'
                    )
                    ->nullable()
                    ->after(
                        'url_maps'
                    );


                /*
                |--------------------------------------------------------------------------
                | Eliminar coordenadas
                |--------------------------------------------------------------------------
                */

                $table->dropColumn([
                    'latitud',
                    'longitud'
                ]);

            }
        );

    }


    public function down(): void
    {

        Schema::table(
            'sucursal',
            function (
                Blueprint $table
            ): void {

                /*
                |--------------------------------------------------------------------------
                | Restaurar coordenadas
                |--------------------------------------------------------------------------
                */

                $table
                    ->decimal(
                        'latitud',
                        10,
                        8
                    )
                    ->nullable()
                    ->after(
                        'correo'
                    );


                $table
                    ->decimal(
                        'longitud',
                        11,
                        8
                    )
                    ->nullable()
                    ->after(
                        'latitud'
                    );


                /*
                |--------------------------------------------------------------------------
                | Eliminar URL embed
                |--------------------------------------------------------------------------
                */

                $table->dropColumn(
                    'url_maps_embed'
                );

            }
        );

    }

};
