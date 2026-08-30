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
            'venta_detalle',
            function (Blueprint $table) {

                $table->id(
                    'id_venta_detalle'
                );

                $table->unsignedBigInteger(
                    'id_venta'
                );

                $table->unsignedBigInteger(
                    'id_producto_variante'
                );

                $table->decimal(
                    'cantidad',
                    15,
                    3
                );

                $table->decimal(
                    'precio_unitario',
                    15,
                    2
                );

                $table->decimal(
                    'costo_unitario',
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
                    'subtotal',
                    15,
                    2
                );

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
                    'id_venta',
                    'fk_venta_det_venta'
                )
                    ->references('id_venta')
                    ->on('venta')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                $table->foreign(
                    'id_producto_variante',
                    'fk_venta_det_variante'
                )
                    ->references(
                        'id_producto_variante'
                    )
                    ->on('producto_variante')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                /*
                |--------------------------------------------------------------------------
                | Restricciones
                |--------------------------------------------------------------------------
                */

                $table->unique(
                    [
                        'id_venta',
                        'id_producto_variante'
                    ],
                    'uq_venta_det_variante'
                );

                $table->index(
                    'estado_registro',
                    'idx_venta_det_estado'
                );
            }
        );
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('venta_detalle');
    }
};