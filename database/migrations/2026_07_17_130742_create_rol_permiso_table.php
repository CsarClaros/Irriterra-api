<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| MOD-002 - SEGURIDAD
|--------------------------------------------------------------------------
| Tabla: rol_permiso
|--------------------------------------------------------------------------
| Relaciona los roles con los permisos del sistema.
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    /**
     * Ejecuta la migración.
     */
    public function up(): void
    {
        Schema::create('rol_permiso', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Clave primaria
            |--------------------------------------------------------------------------
            */

            $table->id('id_rol_permiso');

            /*
            |--------------------------------------------------------------------------
            | Relaciones
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('id_rol')
                ->comment('Rol asociado.');

            $table->unsignedBigInteger('id_permiso')
                ->comment('Permiso asociado.');

            /*
            |--------------------------------------------------------------------------
            | Estado
            |--------------------------------------------------------------------------
            */

            $table->char('estado_registro',1)
                ->default('A');

            /*
            |--------------------------------------------------------------------------
            | Auditoría
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('usuario_creacion')
                ->nullable();

            $table->unsignedBigInteger('usuario_modificacion')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Fechas
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Restricciones
            |--------------------------------------------------------------------------
            */

            $table->foreign('id_rol')
                ->references('id_rol')
                ->on('rol')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('id_permiso')
                ->references('id_permiso')
                ->on('permiso')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Evita registros duplicados
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'id_rol',
                'id_permiso'
            ]);

        });
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('rol_permiso');
    }
};