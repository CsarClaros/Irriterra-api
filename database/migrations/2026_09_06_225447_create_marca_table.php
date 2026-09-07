<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create(
            'marca',
            function (Blueprint $table) {

                $table->id(
                    'id_marca'
                );


                $table->string(
                    'nombre',
                    150
                )->unique();


                $table->string(
                    'slug',
                    180
                )->unique();


                $table->string(
                    'pais',
                    100
                )->nullable();


                $table->string(
                    'logo',
                    255
                )->nullable();


                $table->string(
                    'sitio_web',
                    255
                )->nullable();


                $table->unsignedInteger(
                    'orden'
                )->default(0);


                $table->text(
                    'observaciones'
                )->nullable();


                $table->char(
                    'estado_registro',
                    1
                )->default('A');


                $table->timestamps();

            }
        );

    }


    public function down(): void
    {

        Schema::dropIfExists(
            'marca'
        );

    }

};
