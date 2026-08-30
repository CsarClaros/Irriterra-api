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
            'compra',
            function (Blueprint $table) {

                $table->id(
                    'id_compra'
                );

                $table->string(
                    'codigo_compra',
                    50
                )
                    ->unique();

                $table->unsignedBigInteger(
                    'id_sucursal'
                );

                $table->unsignedBigInteger(
                    'id_proveedor'
                );

                $table->unsignedBigInteger(
                    'id_usuario_comprador'
                );

                $table->string(
                    'estado_compra',
                    20
                )
                    ->default('BORRADOR');

                $table->timestamp(
                    'fecha_compra'
                )
                    ->useCurrent();

                $table->timestamp(
                    'fecha_confirmacion'
                )
                    ->nullable();

                $table->timestamp(
                    'fecha_recepcion'
                )
                    ->nullable();

                $table->timestamp(
                    'fecha_anulacion'
                )
                    ->nullable();

                $table->string(
                    'numero_factura',
                    50
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

                $table->unsignedBigInteger(
                    'id_usuario_confirmacion'
                )
                    ->nullable();

                $table->unsignedBigInteger(
                    'id_usuario_recepcion'
                )
                    ->nullable();

                $table->unsignedBigInteger(
                    'id_usuario_anulacion'
                )
                    ->nullable();

                $table->string(
                    'motivo_anulacion',
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
                | Claves foráneas
                |--------------------------------------------------------------------------
                */

                $table->foreign(
                    'id_sucursal',
                    'fk_compra_sucursal'
                )
                    ->references(
                        'id_sucursal'
                    )
                    ->on('sucursal')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                $table->foreign(
                    'id_proveedor',
                    'fk_compra_proveedor'
                )
                    ->references(
                        'id_proveedor'
                    )
                    ->on('proveedor')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                $table->foreign(
                    'id_usuario_comprador',
                    'fk_compra_comprador'
                )
                    ->references(
                        'id_usuario'
                    )
                    ->on('usuario')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                $table->foreign(
                    'id_usuario_confirmacion',
                    'fk_compra_confirmacion'
                )
                    ->references(
                        'id_usuario'
                    )
                    ->on('usuario')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                $table->foreign(
                    'id_usuario_recepcion',
                    'fk_compra_recepcion'
                )
                    ->references(
                        'id_usuario'
                    )
                    ->on('usuario')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                $table->foreign(
                    'id_usuario_anulacion',
                    'fk_compra_anulacion'
                )
                    ->references(
                        'id_usuario'
                    )
                    ->on('usuario')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                /*
                |--------------------------------------------------------------------------
                | Restricciones e índices
                |--------------------------------------------------------------------------
                */

                $table->unique(
                    [
                        'id_proveedor',
                        'numero_factura'
                    ],
                    'uq_compra_proveedor_factura'
                );

                $table->index(
                    'estado_compra',
                    'idx_compra_estado'
                );

                $table->index(
                    'fecha_compra',
                    'idx_compra_fecha'
                );

                $table->index(
                    'estado_registro',
                    'idx_compra_registro'
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
            'compra'
        );
    }
};