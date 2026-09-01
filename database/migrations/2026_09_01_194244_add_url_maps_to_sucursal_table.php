<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::table(
            'sucursal',
            function (
                Blueprint $table
            ): void {

                $table
                    ->text(
                        'url_maps'
                    )
                    ->nullable()
                    ->after(
                        'longitud'
                    );

            }
        );

    }


    public function down(): void
    {

        Schema::table(
            'sucursal',
            function (
                Blueprint $table
            ): void {

                $table->dropColumn(
                    'url_maps'
                );

            }
        );

    }

};
