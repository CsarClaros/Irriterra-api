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
     * Crea la tabla sucursal.
     *
     * Cada sucursal pertenece a una empresa y representa una sede física donde
     * se realizan operaciones de ventas, inventario y administración.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('sucursal', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Llave primaria
            |--------------------------------------------------------------------------
            */

            $table->id('id_sucursal');

            /*
            |--------------------------------------------------------------------------
            | Empresa
            |--------------------------------------------------------------------------
            */

            $table->foreignId('id_empresa')
                ->constrained('empresa', 'id_empresa')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Información General
            |--------------------------------------------------------------------------
            */

            $table->string('codigo', 20)->unique();
            $table->string('nombre', 150);

            /*
            |--------------------------------------------------------------------------
            | Ubicación
            |--------------------------------------------------------------------------
            */

            $table->string('departamento', 100);
            $table->string('ciudad', 100);
            $table->string('direccion', 255);

            /*
            |--------------------------------------------------------------------------
            | Información de Contacto
            |--------------------------------------------------------------------------
            */

            $table->string('telefono', 30)->nullable();
            $table->string('correo', 150)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Geolocalización
            |--------------------------------------------------------------------------
            |
            | Estos campos serán utilizados en futuras versiones para integrar
            | mapas o funcionalidades de ubicación geográfica.
            |
            */

            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();

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
            */

            $table->char('estado_registro', 1)
                ->default('A')
                ->comment('A=Activo, I=Inactivo, S=Suspendido, E=Eliminado');

            /*
            |--------------------------------------------------------------------------
            | Auditoría
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Índices
            |--------------------------------------------------------------------------
            */

            $table->index('nombre', 'idx_sucursal_nombre');
            $table->index('estado_registro', 'idx_sucursal_estado');
            $table->index('departamento', 'idx_sucursal_departamento');
            $table->index('ciudad', 'idx_sucursal_ciudad');
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
        Schema::dropIfExists('sucursal');
    }
};
