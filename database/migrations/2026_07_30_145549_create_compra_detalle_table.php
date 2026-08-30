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
            'compra_detalle',
            function (Blueprint $table) {

                $table->id(
                    'id_compra_detalle'
                );

                $table->unsignedBigInteger(
                    'id_compra'
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
                    'costo_unitario',
                    15,
                    2
                );

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
                | Claves foráneas
                |--------------------------------------------------------------------------
                */

                $table->foreign(
                    'id_compra',
                    'fk_compra_det_compra'
                )
                    ->references(
                        'id_compra'
                    )
                    ->on('compra')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                $table->foreign(
                    'id_producto_variante',
                    'fk_compra_det_variante'
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
                        'id_compra',
                        'id_producto_variante'
                    ],
                    'uq_compra_det_variante'
                );

                $table->index(
                    'estado_registro',
                    'idx_compra_det_estado'
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
            'compra_detalle'
        );
    }
};