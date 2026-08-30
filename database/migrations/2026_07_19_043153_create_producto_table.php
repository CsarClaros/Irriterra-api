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
        Schema::create('producto', function (Blueprint $table) {

            $table->id('id_producto');

            $table->unsignedBigInteger('id_categoria');

            $table->string('nombre', 150);

            $table->string('marca', 100)
                ->nullable();

            $table->string('modelo', 100)
                ->nullable();

            $table->text('descripcion')
                ->nullable();

            $table->string('catalogo_pdf', 255)
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

            $table->foreign('id_categoria')
                ->references('id_categoria')
                ->on('categoria')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->index('id_categoria');

            $table->index('nombre');

            $table->index('marca');

            $table->index('modelo');

            $table->index('estado_registro');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto');
    }
};