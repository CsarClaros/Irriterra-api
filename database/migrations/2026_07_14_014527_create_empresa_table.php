<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * -------------------------------------------------------------------------
     * Ejecuta la migración.
     * -------------------------------------------------------------------------
     *
     * Crea la tabla empresa, encargada de almacenar la información institucional
     * de la empresa propietaria del ERP.
     *
     * Actualmente el sistema administrará únicamente a Irriterra, sin embargo,
     * el diseño permite soportar múltiples empresas en futuras versiones.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('empresa', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Llave primaria
            |--------------------------------------------------------------------------
            */

            $table->id('id_empresa');

            /*
            |--------------------------------------------------------------------------
            | Información General
            |--------------------------------------------------------------------------
            */

            $table->string('nombre', 150);
            $table->string('nit', 30)->unique();

            /*
            |--------------------------------------------------------------------------
            | Información de Contacto
            |--------------------------------------------------------------------------
            */

            $table->string('telefono', 30)->nullable();
            $table->string('correo', 150)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('sitio_web', 255)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Recursos Multimedia
            |--------------------------------------------------------------------------
            |
            | Se almacena únicamente la ruta del archivo.
            | El archivo físico permanecerá en el servidor.
            |
            */

            $table->string('logo', 255)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Observaciones
            |--------------------------------------------------------------------------
            */

            $table->text('observaciones')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Estado del Registro
            |--------------------------------------------------------------------------
            |
            | A = Activo
            | I = Inactivo
            | S = Suspendido
            | E = Eliminado (Lógico)
            |
            */

            $table->char('estado_registro', 1)
                ->default('A')
                ->comment('A=Activo, I=Inactivo, S=Suspendido, E=Eliminado');

            /*
            |--------------------------------------------------------------------------
            | Auditoría
            |--------------------------------------------------------------------------
            |
            | Los campos created_by y updated_by serán agregados posteriormente,
            | cuando se implemente el módulo de Usuarios.
            |
            */

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Índices
            |--------------------------------------------------------------------------
            */

            $table->index('nombre', 'idx_empresa_nombre');
            $table->index('estado_registro', 'idx_empresa_estado');
        });
    }

    /**
     * -------------------------------------------------------------------------
     * Revierte la migración.
     * -------------------------------------------------------------------------
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa');
    }
};
