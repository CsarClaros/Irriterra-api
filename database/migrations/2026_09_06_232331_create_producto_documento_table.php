<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {

    public function up(): void
    {

        Schema::create(
            'producto_documento',
            function (Blueprint $table) {

                $table->id(
                    'id_producto_documento'
                );


                $table->unsignedBigInteger(
                    'id_producto'
                );


                $table->string(
                    'tipo',
                    30
                );


                $table->string(
                    'nombre',
                    150
                );


                $table->string(
                    'archivo',
                    255
                );


                $table->boolean(
                    'es_publico'
                )->default(
                    true
                );


                $table->unsignedInteger(
                    'orden'
                )->default(
                    0
                );


                $table->text(
                    'observaciones'
                )->nullable();


                $table->char(
                    'estado_registro',
                    1
                )->default(
                    'A'
                );


                $table->timestamps();


                $table->foreign(
                    'id_producto',
                    'producto_documento_producto_fk'
                )
                    ->references(
                        'id_producto'
                    )
                    ->on(
                        'producto'
                    )
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();


                $table->index(
                    [
                        'id_producto',
                        'estado_registro'
                    ],
                    'producto_documento_producto_estado_index'
                );


                $table->index(
                    [
                        'es_publico',
                        'estado_registro'
                    ],
                    'producto_documento_publico_estado_index'
                );

            }
        );

    }


    public function down(): void
    {

        Schema::dropIfExists(
            'producto_documento'
        );

    }

};
