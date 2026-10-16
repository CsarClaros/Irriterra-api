<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table(
            'compra_detalle',
            function (Blueprint $table) {

                /*
                 * Valores tal como fueron negociados
                 * en la moneda original de la compra.
                 */

                $table->decimal(
                    'costo_unitario_original',
                    15,
                    2
                )
                    ->default(0)
                    ->after('costo_unitario');

                $table->decimal(
                    'descuento_original',
                    15,
                    2
                )
                    ->default(0)
                    ->after('descuento');

                $table->decimal(
                    'subtotal_original',
                    15,
                    2
                )
                    ->default(0)
                    ->after('subtotal');
            }
        );

        /*
         * Los registros históricos existentes
         * corresponden a BOB.
         */
        DB::table('compra_detalle')
            ->update([

                'costo_unitario_original' =>
                    DB::raw('costo_unitario'),

                'descuento_original' =>
                    DB::raw('descuento'),

                'subtotal_original' =>
                    DB::raw('subtotal')

            ]);
    }

    public function down(): void
    {
        Schema::table(
            'compra_detalle',
            function (Blueprint $table) {

                $table->dropColumn([

                    'costo_unitario_original',

                    'descuento_original',

                    'subtotal_original'

                ]);
            }
        );
    }
};
