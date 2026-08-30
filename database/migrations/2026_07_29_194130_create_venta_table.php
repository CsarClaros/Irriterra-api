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
            'venta',
            function (Blueprint $table) {

                $table->id('id_venta');

                $table->string(
                    'codigo_venta',
                    50
                )
                    ->unique();

                $table->unsignedBigInteger(
                    'id_sucursal'
                );

                $table->unsignedBigInteger(
                    'id_cliente'
                )
                    ->nullable();

                $table->unsignedBigInteger(
                    'id_usuario_vendedor'
                );

                $table->string(
                    'estado_venta',
                    20
                )
                    ->default('BORRADOR');

                $table->timestamp(
                    'fecha_venta'
                )
                    ->useCurrent();

                $table->timestamp(
                    'fecha_pago'
                )
                    ->nullable();

                $table->timestamp(
                    'fecha_anulacion'
                )
                    ->nullable();

                $table->decimal(
                    'subtotal',
                    15,
                    2
                )
                    ->default(0);

                $table->decimal(
                    'descuento',
                    15,
                    2
                )
                    ->default(0);

                $table->decimal(
                    'total',
                    15,
                    2
                )
                    ->default(0);

                $table->decimal(
                    'monto_pagado',
                    15,
                    2
                )
                    ->default(0);

                $table->string(
                    'metodo_pago',
                    30
                )
                    ->nullable();

                $table->string(
                    'motivo_anulacion',
                    255
                )
                    ->nullable();

                $table->unsignedBigInteger(
                    'id_usuario_anulacion'
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

                /*
                |--------------------------------------------------------------------------
                | Claves foráneas
                |--------------------------------------------------------------------------
                */

                $table->foreign(
                    'id_sucursal',
                    'fk_venta_sucursal'
                )
                    ->references('id_sucursal')
                    ->on('sucursal')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                $table->foreign(
                    'id_cliente',
                    'fk_venta_cliente'
                )
                    ->references('id_cliente')
                    ->on('cliente')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                $table->foreign(
                    'id_usuario_vendedor',
                    'fk_venta_vendedor'
                )
                    ->references('id_usuario')
                    ->on('usuario')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                $table->foreign(
                    'id_usuario_anulacion',
                    'fk_venta_usuario_anulacion'
                )
                    ->references('id_usuario')
                    ->on('usuario')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                /*
                |--------------------------------------------------------------------------
                | Índices
                |--------------------------------------------------------------------------
                */

                $table->index(
                    'estado_venta',
                    'idx_venta_estado'
                );

                $table->index(
                    'fecha_venta',
                    'idx_venta_fecha'
                );

                $table->index(
                    'estado_registro',
                    'idx_venta_registro'
                );
            }
        );
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('venta');
    }
};