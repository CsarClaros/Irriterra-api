<?php

namespace Database\Seeders\Compras;

use App\Models\Compras\Proveedor;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    /**
     * Ejecuta el seeder.
     */
    public function run(): void
    {
        Proveedor::firstOrCreate(

            [
                'numero_documento' =>
                    '1020304050'
            ],

            [
                'tipo_proveedor' =>
                    Proveedor::EMPRESA,

                'nombre_razon_social' =>
                    'Proveedor Inicial S.R.L.',

                'tipo_documento' =>
                    'NIT',

                'nombre_contacto' =>
                    'Responsable Comercial',

                'telefono' =>
                    '70000000',

                'correo' =>
                    'proveedor@example.com',

                'direccion' =>
                    'Zona Industrial',

                'ciudad' =>
                    'Santa Cruz de la Sierra',

                'departamento' =>
                    'Santa Cruz',

                'observaciones' =>
                    'Proveedor inicial generado desde el seeder.',

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