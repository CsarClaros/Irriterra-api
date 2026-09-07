<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::table(
            'precio_producto_variante',
            function (Blueprint $table) {

                $table->decimal(
                    'costo_compra',
                    15,
                    2
                )
                    ->nullable()
                    ->default(null)
                    ->change();


                $table->decimal(
                    'precio_minimo',
                    15,
                    2
                )
                    ->nullable()
                    ->default(null)
                    ->change();


                $table->decimal(
                    'precio_venta',
                    15,
                    2
                )
                    ->nullable()
                    ->default(null)
                    ->change();

            }
        );

    }


    public function down(): void
    {

        Schema::table(
            'precio_producto_variante',
            function (Blueprint $table) {

                $table->decimal(
                    'costo_compra',
                    15,
                    2
                )
                    ->default(0)
                    ->nullable(false)
                    ->change();


                $table->decimal(
                    'precio_minimo',
                    15,
                    2
                )
                    ->default(0)
                    ->nullable(false)
                    ->change();


                $table->decimal(
                    'precio_venta',
                    15,
                    2
                )
                    ->default(0)
                    ->nullable(false)
                    ->change();

            }
        );

    }

};
