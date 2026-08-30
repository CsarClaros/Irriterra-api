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
        Schema::create('producto_imagen', function (Blueprint $table) {

            $table->id('id_producto_imagen');

            $table->unsignedBigInteger('id_producto');

            $table->string('ruta_imagen', 255);

            $table->string('texto_alternativo', 255)
                ->nullable();

            $table->unsignedInteger('orden')
                ->default(1);

            $table->boolean('es_principal')
                ->default(false);

            $table->text('observaciones')
                ->nullable();

            $table->char('estado_registro', 1)
                ->default('A');

            $table->unsignedBigInteger('usuario_creacion')
                ->nullable();

            $table->unsignedBigInteger('usuario_modificacion')
                ->nullable();

            $table->timestamps();

            $table->foreign('id_producto')
                ->references('id_producto')
                ->on('producto')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->unique(
                [
                    'id_producto',
                    'ruta_imagen'
                ],
                'producto_imagen_ruta_unique'
            );

            $table->index('id_producto');

            $table->index('orden');

            $table->index('es_principal');

            $table->index('estado_registro');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_imagen');
    }
};