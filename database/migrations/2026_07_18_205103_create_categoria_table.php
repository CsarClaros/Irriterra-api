<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración.
     */
    public function up(): void
    {
        Schema::create('categoria', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Clave primaria
            |--------------------------------------------------------------------------
            */

            $table->id('id_categoria');

            /*
            |--------------------------------------------------------------------------
            | Información General
            |--------------------------------------------------------------------------
            */

            $table->string('nombre', 150)->unique();

            $table->string('descripcion', 255)->nullable();

            $table->text('observaciones')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Estado
            |--------------------------------------------------------------------------
            */

            $table->char('estado_registro', 1)
                ->default('A');

            /*
            |--------------------------------------------------------------------------
            | Auditoría
            |--------------------------------------------------------------------------
            */

            $table->timestamps();
        });
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('categoria');
    }
};
