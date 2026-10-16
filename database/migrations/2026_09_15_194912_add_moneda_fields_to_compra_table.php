<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table(
            'compra',
            function (Blueprint $table) {

                /*
                |--------------------------------------------------------------------------
                | Moneda utilizada originalmente
                |--------------------------------------------------------------------------
                */

                $table->char(
                    'moneda',
                    3
                )
                    ->default('BOB')
                    ->after('numero_factura');

                /*
                |--------------------------------------------------------------------------
                | Tipo de cambio
                |--------------------------------------------------------------------------
                */

                $table->decimal(
                    'tipo_cambio_referencia',
                    15,
                    6
                )
                    ->nullable()
                    ->after('moneda');

                $table->decimal(
                    'tipo_cambio_aplicado',
                    15,
                    6
                )
                    ->default(1)
                    ->after('tipo_cambio_referencia');

                $table->string(
                    'origen_tipo_cambio',
                    20
                )
                    ->default('NO_APLICA')
                    ->after('tipo_cambio_aplicado');

                $table->string(
                    'fuente_tipo_cambio',
                    100
                )
                    ->nullable()
                    ->after('origen_tipo_cambio');

                $table->timestamp(
                    'fecha_tipo_cambio'
                )
                    ->nullable()
                    ->after('fuente_tipo_cambio');

                /*
                |--------------------------------------------------------------------------
                | Importes originales
                |--------------------------------------------------------------------------
                |
                | subtotal, descuento y total actuales continúan
                | representando BOB.
                |
                */

                $table->decimal(
                    'subtotal_original',
                    15,
                    2
                )
                    ->default(0);

                $table->decimal(
                    'descuento_original',
                    15,
                    2
                )
                    ->default(0);

                $table->decimal(
                    'total_original',
                    15,
                    2
                )
                    ->default(0);

                $table->index(
                    'moneda',
                    'idx_compra_moneda'
                );
            }
        );

        /*
         * Todas las compras existentes fueron
         * registradas originalmente en BOB.
         */
        DB::table('compra')
            ->update([

                'moneda' =>
                    'BOB',

                'tipo_cambio_referencia' =>
                    null,

                'tipo_cambio_aplicado' =>
                    1,

                'origen_tipo_cambio' =>
                    'NO_APLICA',

                'fuente_tipo_cambio' =>
                    null,

                'fecha_tipo_cambio' =>
                    null,

                'subtotal_original' =>
                    DB::raw('subtotal'),

                'descuento_original' =>
                    DB::raw('descuento'),

                'total_original' =>
                    DB::raw('total')

            ]);
    }

    public function down(): void
    {
        Schema::table(
            'compra',
            function (Blueprint $table) {

                $table->dropIndex(
                    'idx_compra_moneda'
                );

                $table->dropColumn([

                    'moneda',

                    'tipo_cambio_referencia',

                    'tipo_cambio_aplicado',

                    'origen_tipo_cambio',

                    'fuente_tipo_cambio',

                    'fecha_tipo_cambio',

                    'subtotal_original',

                    'descuento_original',

                    'total_original'

                ]);
            }
        );
    }
};
