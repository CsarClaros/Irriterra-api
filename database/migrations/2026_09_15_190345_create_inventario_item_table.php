<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create(
            'inventario_item',
            function (Blueprint $table) {

                $table->id(
                    'id_inventario_item'
                );

                $table->unsignedBigInteger(
                    'id_producto_variante'
                );

                /*
                 * Es nullable porque un item vendido
                 * o en tránsito ya no pertenece al
                 * stock físico de una sucursal.
                 */
                $table->unsignedBigInteger(
                    'id_stock_sucursal'
                )
                    ->nullable();

                /*
                 * Compra desde la que ingresó
                 * originalmente la unidad.
                 *
                 * Nullable porque en el futuro
                 * podrían existir ajustes de entrada.
                 */
                $table->unsignedBigInteger(
                    'id_compra_detalle_origen'
                )
                    ->nullable();

                /*
                 * Número de serie interno generado
                 * exclusivamente por Irriterra.
                 */
                $table->string(
                    'numero_serie',
                    100
                )
                    ->unique();

                $table->string(
                    'estado_item',
                    30
                )
                    ->default('DISPONIBLE');

                $table->timestamp(
                    'fecha_ingreso'
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
                | Relaciones
                |--------------------------------------------------------------------------
                */

                $table->foreign(
                    'id_producto_variante',
                    'fk_inv_item_variante'
                )
                    ->references(
                        'id_producto_variante'
                    )
                    ->on(
                        'producto_variante'
                    )
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                $table->foreign(
                    'id_stock_sucursal',
                    'fk_inv_item_stock'
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
                    'id_compra_detalle_origen',
                    'fk_inv_item_compra_det'
                )
                    ->references(
                        'id_compra_detalle'
                    )
                    ->on(
                        'compra_detalle'
                    )
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                /*
                |--------------------------------------------------------------------------
                | Índices
                |--------------------------------------------------------------------------
                */

                $table->index(
                    'id_producto_variante',
                    'idx_inv_item_variante'
                );

                $table->index(
                    'id_stock_sucursal',
                    'idx_inv_item_stock'
                );

                $table->index(
                    'id_compra_detalle_origen',
                    'idx_inv_item_compra_det'
                );

                $table->index(
                    'estado_item',
                    'idx_inv_item_estado'
                );

                $table->index(
                    'estado_registro',
                    'idx_inv_item_registro'
                );

                /*
                 * Facilita buscar rápidamente
                 * items disponibles de una variante
                 * dentro de una sucursal.
                 */
                $table->index(
                    [
                        'id_stock_sucursal',
                        'estado_item'
                    ],
                    'idx_inv_item_stock_estado'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'inventario_item'
        );
    }
};
