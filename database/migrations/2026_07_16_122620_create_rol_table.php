<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| MOD-002 - SEGURIDAD
|--------------------------------------------------------------------------
| Tabla: rol
|--------------------------------------------------------------------------
| Esta tabla almacena los roles del sistema ERP.
|
| Cada usuario pertenece a un único rol.
|
| Ejemplos:
| - Administrador
| - Gerente
| - Supervisor
| - Vendedor
| - Almacenero
|
| Los permisos específicos serán administrados mediante la tabla
| rol_permiso.
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rol', function (Blueprint $table) {
            /*
            |--------------------------------------------------------------------------
            | Clave primaria
            |--------------------------------------------------------------------------
            */
            $table->id('id_rol')
                ->comment('Identificador único del rol.');

            /*
            |--------------------------------------------------------------------------
            | Información general
            |--------------------------------------------------------------------------
            */

            $table->string('nombre', 100)
                ->unique()
                ->comment('Nombre único del rol.');

            $table->string('descripcion', 255)
                ->nullable()
                ->comment('Descripción del rol.');

            /*
            |--------------------------------------------------------------------------
            | Estado del registro
            |--------------------------------------------------------------------------
            */

            $table->char('estado_registro', 1)
                ->default('A')
                ->comment('A=Activo | I=Inactivo');

            /*
            |--------------------------------------------------------------------------
            | Auditoría
            |--------------------------------------------------------------------------
            |
            | Se utilizarán cuando el módulo de Seguridad esté completamente
            | implementado.
            |
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
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rol');
    }
};
