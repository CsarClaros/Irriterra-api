<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create(
            'movimiento_inventario_item',
            function (Blueprint $table) {

                $table->id(
                    'id_movimiento_inventario_item'
                );

                $table->unsignedBigInteger(
                    'id_movimiento_inventario'
                );

                $table->unsignedBigInteger(
                    'id_inventario_item'
                );

                /*
                 * Guardamos el estado anterior
                 * y resultante para conservar
                 * la trazabilidad histórica.
                 */
                $table->string(
                    'estado_anterior',
                    30
                )
                    ->nullable();

                $table->string(
                    'estado_resultante',
                    30
                );

                /*
                 * Ubicación física anterior
                 * y resultante del item.
                 */
                $table->unsignedBigInteger(
                    'id_stock_sucursal_anterior'
                )
                    ->nullable();

                $table->unsignedBigInteger(
                    'id_stock_sucursal_resultante'
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

                $table->timestamps();

                $table->foreign(
                    'id_movimiento_inventario',
                    'fk_mov_inv_item_mov'
                )
                    ->references(
                        'id_movimiento_inventario'
                    )
                    ->on(
                        'movimiento_inventario'
                    )
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                $table->foreign(
                    'id_inventario_item',
                    'fk_mov_inv_item_item'
                )
                    ->references(
                        'id_inventario_item'
                    )
                    ->on(
                        'inventario_item'
                    )
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                $table->foreign(
                    'id_stock_sucursal_anterior',
                    'fk_mov_item_stock_ant'
                )
                    ->references(
                        'id_stock_sucursal'
                    )
                    ->on(
                        'stock_sucursal'
                    )
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                $table->foreign(
                    'id_stock_sucursal_resultante',
                    'fk_mov_item_stock_res'
                )
                    ->references(
                        'id_stock_sucursal'
                    )
                    ->on(
                        'stock_sucursal'
                    )
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                /*
                 * Un mismo item no puede aparecer
                 * dos veces en el mismo movimiento.
                 */
                $table->unique(
                    [
                        'id_movimiento_inventario',
                        'id_inventario_item'
                    ],
                    'uq_mov_inv_item'
                );

                $table->index(
                    'id_inventario_item',
                    'idx_mov_inv_item_item'
                );

                $table->index(
                    'estado_registro',
                    'idx_mov_inv_item_estado'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'movimiento_inventario_item'
        );
    }
};
