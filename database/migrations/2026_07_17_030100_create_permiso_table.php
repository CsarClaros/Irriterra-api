<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| MOD-002 - SEGURIDAD
|--------------------------------------------------------------------------
| Tabla: permiso
|--------------------------------------------------------------------------
| Almacena todos los permisos disponibles del sistema ERP.
|
| Cada permiso podrá ser asignado posteriormente a uno o varios roles.
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    /**
     * Ejecuta la migración.
     */
    public function up(): void
    {
        Schema::create('permiso', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Clave primaria
            |--------------------------------------------------------------------------
            */

            $table->id('id_permiso')
                ->comment('Identificador único del permiso.');

            /*
            |--------------------------------------------------------------------------
            | Información general
            |--------------------------------------------------------------------------
            */

            $table->string('nombre', 150)
                ->unique()
                ->comment('Nombre único del permiso.');

            $table->string('descripcion', 255)
                ->nullable()
                ->comment('Descripción del permiso.');

            /*
            |--------------------------------------------------------------------------
            | Estado
            |--------------------------------------------------------------------------
            */

            $table->char('estado_registro', 1)
                ->default('A')
                ->comment('A = Activo | I = Inactivo');

            /*
            |--------------------------------------------------------------------------
            | Auditoría
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('usuario_creacion')
                ->nullable()
                ->comment('Usuario que creó el registro.');

            $table->unsignedBigInteger('usuario_modificacion')
                ->nullable()
                ->comment('Último usuario que modificó el registro.');

            /*
            |--------------------------------------------------------------------------
            | Fechas
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
        Schema::dropIfExists('permiso');
    }
};