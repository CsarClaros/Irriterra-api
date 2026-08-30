<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones.
     */
    public function up(): void
    {
        Schema::create(
            'precio_producto_variante',
            function (Blueprint $table) {

                $table->id(
                    'id_precio_producto_variante'
                );

                $table->unsignedBigInteger(
                    'id_producto_variante'
                );

                $table->decimal(
                    'costo_compra',
                    15,
                    2
                )
                    ->default(0);

                $table->decimal(
                    'precio_minimo',
                    15,
                    2
                )
                    ->default(0);

                $table->decimal(
                    'precio_venta',
                    15,
                    2
                )
                    ->default(0);

                $table->timestamp(
                    'fecha_vigencia'
                )
                    ->useCurrent();

                $table->text(
                    'observaciones'
                )
                    ->nullable();

                $table->char(
                    'estado_registro',
                    1
                )
                    ->default('A');

                $table->unsignedBigInteger(
                    'usuario_creacion'
                )
                    ->nullable();

                $table->unsignedBigInteger(
                    'usuario_modificacion'
                )
                    ->nullable();

                $table->timestamps();

                $table->foreign(
                    'id_producto_variante'
                )
                    ->references(
                        'id_producto_variante'
                    )
                    ->on('producto_variante')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                $table->unique(
                    'id_producto_variante',
                    'precio_producto_variante_unique'
                );

                $table->index(
                    'fecha_vigencia'
                );

                $table->index(
                    'estado_registro'
                );
            }
        );
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'precio_producto_variante'
        );
    }
};