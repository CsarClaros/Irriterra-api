<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {

    public function up(): void
    {

        Schema::table(
            'producto',
            function (Blueprint $table) {

                $table->boolean(
                    'controla_stock'
                )
                    ->default(true)
                    ->after(
                        'catalogo_pdf'
                    )
                    ->index();

            }
        );

    }


    public function down(): void
    {

        Schema::table(
            'producto',
            function (Blueprint $table) {

                $table->dropIndex([
                    'controla_stock'
                ]);

                $table->dropColumn(
                    'controla_stock'
                );

            }
        );

    }

};
