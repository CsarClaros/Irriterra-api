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
        Schema::create(
            'proveedor',
            function (Blueprint $table) {

                $table->id(
                    'id_proveedor'
                );

                $table->string(
                    'tipo_proveedor',
                    20
                )
                    ->default('EMPRESA');

                $table->string(
                    'nombre_razon_social',
                    150
                );

                $table->string(
                    'tipo_documento',
                    20
                )
                    ->nullable();

                $table->string(
                    'numero_documento',
                    30
                )
                    ->nullable()
                    ->unique();

                $table->string(
                    'nombre_contacto',
                    150
                )
                    ->nullable();

                $table->string(
                    'telefono',
                    30
                )
                    ->nullable();

                $table->string(
                    'correo',
                    150
                )
                    ->nullable();

                $table->string(
                    'direccion',
                    255
                )
                    ->nullable();

                $table->string(
                    'ciudad',
                    100
                )
                    ->nullable();

                $table->string(
                    'departamento',
                    100
                )
                    ->nullable();

                $table->text(
                    'observaciones'
                )
                    ->nullable();

                $table->char(
                    'estado_registro',
                    1
                )
                    ->default('A');

                $table->unsignedBigInteger(
                    'usuario_creacion'
                )
                    ->nullable();

                $table->unsignedBigInteger(
                    'usuario_modificacion'
                )
                    ->nullable();

                $table->timestamps();

                /*
                |--------------------------------------------------------------------------
                | Índices
                |--------------------------------------------------------------------------
                */

                $table->index(
                    'tipo_proveedor',
                    'idx_proveedor_tipo'
                );

                $table->index(
                    'nombre_razon_social',
                    'idx_proveedor_nombre'
                );

                $table->index(
                    'estado_registro',
                    'idx_proveedor_estado'
                );
            }
        );
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'proveedor'
        );
    }
};