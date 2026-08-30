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
            'cliente',
            function (Blueprint $table) {

                $table->id('id_cliente');

                $table->string(
                    'tipo_cliente',
                    30
                )
                    ->default('PERSONA');

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

                $table->text('observaciones')
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

                $table->index(
                    'tipo_cliente',
                    'idx_cliente_tipo'
                );

                $table->index(
                    'estado_registro',
                    'idx_cliente_estado'
                );
            }
        );
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente');
    }
};