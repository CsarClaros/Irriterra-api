<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::table(
            'producto_variante',
            function (Blueprint $table) {

                $table->unsignedBigInteger(
                    'id_marca'
                )
                    ->nullable()
                    ->after(
                        'id_producto'
                    );


                $table->index(
                    'id_marca',
                    'producto_variante_marca_index'
                );


                $table->foreign(
                    'id_marca',
                    'producto_variante_marca_fk'
                )
                    ->references(
                        'id_marca'
                    )
                    ->on(
                        'marca'
                    )
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

            }
        );

    }


    public function down(): void
    {

        Schema::table(
            'producto_variante',
            function (Blueprint $table) {

                $table->dropForeign(
                    'producto_variante_marca_fk'
                );


                $table->dropIndex(
                    'producto_variante_marca_index'
                );


                $table->dropColumn(
                    'id_marca'
                );

            }
        );

    }

};
