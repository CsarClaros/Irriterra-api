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
            'transferencia_inventario_detalle',
            function (Blueprint $table) {

                $table->id(
                    'id_transferencia_inventario_detalle'
                );

                $table->unsignedBigInteger(
                    'id_transferencia_inventario'
                );

                $table->unsignedBigInteger(
                    'id_producto_variante'
                );

                $table->decimal(
                    'cantidad',
                    15,
                    3
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

                $table->foreign(
                    'id_transferencia_inventario',
                    'fk_trf_det_transferencia'
                )
                    ->references(
                        'id_transferencia_inventario'
                    )
                    ->on('transferencia_inventario')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                $table->foreign(
                    'id_producto_variante',
                    'fk_trf_det_variante'
                )
                    ->references(
                        'id_producto_variante'
                    )
                    ->on('producto_variante')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                $table->unique(
                    [
                        'id_transferencia_inventario',
                        'id_producto_variante'
                    ],
                    'uq_trf_det_variante'
                );

                // $table->index(
                //     'id_transferencia_inventario'
                // );

                // $table->index(
                //     'id_producto_variante'
                // );

                $table->index(
                    'estado_registro',
                    'idx_trf_det_estado'
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
            'transferencia_inventario_detalle'
        );
    }
};
