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
            'transferencia_inventario',
            function (Blueprint $table) {

                $table->id(
                    'id_transferencia_inventario'
                );

                $table->string(
                    'codigo_transferencia',
                    50
                )
                    ->unique();

                $table->unsignedBigInteger(
                    'id_sucursal_origen'
                );

                $table->unsignedBigInteger(
                    'id_sucursal_destino'
                );

                $table->string(
                    'estado_transferencia',
                    20
                )
                    ->default('PENDIENTE');

                $table->timestamp(
                    'fecha_solicitud'
                )
                    ->useCurrent();

                $table->timestamp(
                    'fecha_envio'
                )
                    ->nullable();

                $table->timestamp(
                    'fecha_recepcion'
                )
                    ->nullable();

                $table->timestamp(
                    'fecha_rechazo'
                )
                    ->nullable();

                $table->unsignedBigInteger(
                    'id_usuario_solicitud'
                )
                    ->nullable();

                $table->unsignedBigInteger(
                    'id_usuario_envio'
                )
                    ->nullable();

                $table->unsignedBigInteger(
                    'id_usuario_recepcion'
                )
                    ->nullable();

                $table->unsignedBigInteger(
                    'id_usuario_rechazo'
                )
                    ->nullable();

                $table->string(
                    'motivo_rechazo',
                    255
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
                | Claves foráneas de sucursales
                |--------------------------------------------------------------------------
                */

                $table->foreign(
                    'id_sucursal_origen'
                )
                    ->references('id_sucursal')
                    ->on('sucursal')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                $table->foreign(
                    'id_sucursal_destino'
                )
                    ->references('id_sucursal')
                    ->on('sucursal')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                /*
                |--------------------------------------------------------------------------
                | Claves foráneas de usuarios
                |--------------------------------------------------------------------------
                */

                $table->foreign(
                    'id_usuario_solicitud'
                )
                    ->references('id_usuario')
                    ->on('usuario')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                $table->foreign(
                    'id_usuario_envio'
                )
                    ->references('id_usuario')
                    ->on('usuario')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                $table->foreign(
                    'id_usuario_recepcion'
                )
                    ->references('id_usuario')
                    ->on('usuario')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                $table->foreign(
                    'id_usuario_rechazo'
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
                    'id_sucursal_origen'
                );

                $table->index(
                    'id_sucursal_destino'
                );

                $table->index(
                    'estado_transferencia'
                );

                $table->index(
                    'fecha_solicitud'
                );

                $table->index(
                    'estado_registro'
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
            'transferencia_inventario'
        );
    }
};