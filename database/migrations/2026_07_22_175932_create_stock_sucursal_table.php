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
        Schema::create('stock_sucursal', function (Blueprint $table) {

            $table->id('id_stock_sucursal');

            $table->unsignedBigInteger('id_sucursal');

            $table->unsignedBigInteger('id_producto_variante');

            $table->decimal('stock_actual', 15, 3)
                ->default(0);

            $table->decimal('stock_minimo', 15, 3)
                ->default(0);

            $table->decimal('stock_maximo', 15, 3)
                ->nullable();

            $table->string('ubicacion_almacen', 150)
                ->nullable();

            $table->text('observaciones')
                ->nullable();

            $table->char('estado_registro', 1)
                ->default('A');

            $table->unsignedBigInteger('usuario_creacion')
                ->nullable();

            $table->unsignedBigInteger('usuario_modificacion')
                ->nullable();

            $table->timestamps();

            $table->foreign('id_sucursal')
                ->references('id_sucursal')
                ->on('sucursal')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('id_producto_variante')
                ->references('id_producto_variante')
                ->on('producto_variante')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->unique(
                [
                    'id_sucursal',
                    'id_producto_variante'
                ],
                'stock_sucursal_variante_unique'
            );

            $table->index('id_producto_variante');

            $table->index('estado_registro');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_sucursal');
    }
};
