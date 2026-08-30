<?php

namespace Database\Seeders\Ventas;

use App\Models\Ventas\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    /**
     * Ejecuta el seeder.
     */
    public function run(): void
    {
        Cliente::firstOrCreate(

            [
                'nombre_razon_social' =>
                    'Consumidor Final'
            ],

            [
                'tipo_cliente' =>
                    Cliente::CONSUMIDOR_FINAL,

                'tipo_documento' =>
                    null,

                'numero_documento' =>
                    null,

                'telefono' =>
                    null,

                'correo' =>
                    null,

                'direccion' =>
                    null,

                'observaciones' =>
                    'Cliente genérico para ventas sin identificación.',

                'estado_registro' =>
                    'A',

                'usuario_creacion' =>
                    null,

                'usuario_modificacion' =>
                    null
            ]

        );
    }
}