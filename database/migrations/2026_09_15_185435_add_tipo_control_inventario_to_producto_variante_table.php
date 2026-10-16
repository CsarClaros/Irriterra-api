<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'producto_variante',
            function (Blueprint $table) {

                $table->string(
                    'tipo_control_inventario',
                    20
                )
                    ->default('CANTIDAD')
                    ->after('unidad_medida');

                $table->index(
                    'tipo_control_inventario',
                    'idx_prod_var_tipo_control'
                );
            }
        );

        /*
         * Compatibilidad con el campo antiguo
         * producto.controla_stock.
         *
         * Los productos que no controlaban stock
         * pasan inicialmente a SIN_CONTROL.
         */
        DB::table('producto_variante')
            ->whereIn(
                'id_producto',
                function ($query) {

                    $query
                        ->select('id_producto')
                        ->from('producto')
                        ->where(
                            'controla_stock',
                            false
                        );
                }
            )
            ->update([
                'tipo_control_inventario' =>
                    'SIN_CONTROL'
            ]);
    }

    public function down(): void
    {
        Schema::table(
            'producto_variante',
            function (Blueprint $table) {

                $table->dropIndex(
                    'idx_prod_var_tipo_control'
                );

                $table->dropColumn(
                    'tipo_control_inventario'
                );
            }
        );
    }
};
