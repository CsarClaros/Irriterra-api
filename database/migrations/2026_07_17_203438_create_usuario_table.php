<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| MOD-002 - SEGURIDAD
|--------------------------------------------------------------------------
| Tabla: usuario
|--------------------------------------------------------------------------
| Almacena los usuarios que accederán al ERP.
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    /**
     * Ejecuta la migración.
     */
    public function up(): void
    {
        Schema::create('usuario', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Clave primaria
            |--------------------------------------------------------------------------
            */

            $table->id('id_usuario');


            /*
            |--------------------------------------------------------------------------
            | Relaciones
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('id_rol')
                ->comment('Rol principal del usuario.');

            $table->unsignedBigInteger('id_sucursal')
                ->comment('Sucursal asignada al usuario.');

            /*
            |--------------------------------------------------------------------------
            | Identificación
            |--------------------------------------------------------------------------
            */

            $table->string('ci', 20)
                ->unique()
                ->comment('Documento de identificacion personal ');

            
            /*
            |--------------------------------------------------------------------------
            | Credenciales
            |--------------------------------------------------------------------------
            */

            $table->string('usuario', 50)
                ->unique()
                ->comment('Nombre de usuario para iniciar sesión.');

            $table->string('password', 255)
                ->comment('Contraseña cifrada.');

            /*
            |--------------------------------------------------------------------------
            | Datos personales
            |--------------------------------------------------------------------------
            */

            $table->string('nombre', 100);

            $table->string('apellido_paterno', 100);

            $table->string('apellido_materno', 100)
                ->nullable();

            $table->string('correo', 150)
                ->nullable()
                ->unique();

            $table->string('telefono', 20)
                ->nullable();

            $table->string('direccion', 255)
                ->nullable();

            $table->string('foto', 255)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Seguridad
            |--------------------------------------------------------------------------
            */

            $table->timestamp('ultimo_acceso')
                ->nullable();

            $table->unsignedTinyInteger('intentos_fallidos')
                ->default(0);

            $table->timestamp('bloqueado_hasta')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Estado
            |--------------------------------------------------------------------------
            */

            $table->char('estado_registro', 1)
                ->default('A')
                ->comment('A=Activo | I=Inactivo');

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
            | Índices
            |--------------------------------------------------------------------------
            */

            $table->index('id_rol');

            $table->index('id_sucursal');

            $table->index('estado_registro');

            /*
            |--------------------------------------------------------------------------
            | Claves foráneas
            |--------------------------------------------------------------------------
            */

            $table->foreign('id_rol')
                ->references('id_rol')
                ->on('rol')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('id_sucursal')
                ->references('id_sucursal')
                ->on('sucursal')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};
