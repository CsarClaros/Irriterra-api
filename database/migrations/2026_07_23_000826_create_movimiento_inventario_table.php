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
            'movimiento_inventario',
            function (Blueprint $table) {

                $table->id(
                    'id_movimiento_inventario'
                );

                $table->unsignedBigInteger(
                    'id_stock_sucursal'
                );

                $table->string(
                    'codigo_movimiento',
                    50
                )
                    ->unique();

                $table->string(
                    'tipo_movimiento',
                    30
                );

                $table->decimal(
                    'cantidad',
                    15,
                    3
                );

                $table->decimal(
                    'stock_anterior',
                    15,
                    3
                );

                $table->decimal(
                    'stock_resultante',
                    15,
                    3
                );

                $table->string(
                    'motivo',
                    150
                );

                $table->string(
                    'tipo_referencia',
                    50
                )
                    ->nullable();

                $table->unsignedBigInteger(
                    'id_referencia'
                )
                    ->nullable();

                $table->timestamp(
                    'fecha_movimiento'
                )
                    ->useCurrent();

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

                $table->foreign(
                    'id_stock_sucursal'
                )
                    ->references(
                        'id_stock_sucursal'
                    )
                    ->on('stock_sucursal')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                $table->index(
                    'id_stock_sucursal'
                );

                $table->index(
                    'tipo_movimiento'
                );

                $table->index(
                    'fecha_movimiento'
                );

                $table->index([
                    'tipo_referencia',
                    'id_referencia'
                ]);

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
            'movimiento_inventario'
        );
    }
};