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
        Schema::create('producto_variante', function (Blueprint $table) {

            $table->id('id_producto_variante');

            $table->unsignedBigInteger('id_producto');

            $table->string('nombre', 150);

            $table->string('sku', 100)
                ->unique();

            $table->string('codigo_comercial', 100)
                ->nullable()
                ->unique();

            $table->string('unidad_medida', 20)
                ->default('UND');

            $table->text('descripcion')
                ->nullable();

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

            $table->index('id_producto');

            $table->index('nombre');

            $table->index('unidad_medida');

            $table->index('estado_registro');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_variante');
    }
};