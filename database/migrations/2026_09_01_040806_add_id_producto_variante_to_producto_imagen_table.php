<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'producto_imagen',
            function (Blueprint $table) {

                $table
                    ->unsignedBigInteger(
                        'id_producto_variante'
                    )
                    ->nullable()
                    ->after(
                        'id_producto'
                    );


                $table->index(
                    'id_producto_variante',
                    'producto_imagen_variante_idx'
                );


                $table->foreign(
                    'id_producto_variante',
                    'producto_imagen_variante_fk'
                )
                    ->references(
                        'id_producto_variante'
                    )
                    ->on(
                        'producto_variante'
                    )
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

            }
        );
    }


    public function down(): void
    {
        Schema::table(
            'producto_imagen',
            function (Blueprint $table) {

                $table->dropForeign(
                    'producto_imagen_variante_fk'
                );


                $table->dropIndex(
                    'producto_imagen_variante_idx'
                );


                $table->dropColumn(
                    'id_producto_variante'
                );

            }
        );
    }
};
