<?php

namespace Database\Seeders\Compras;

use App\Models\Compras\Compra;
use App\Models\Compras\Proveedor;
use App\Models\Organizacion\Sucursal;
use App\Models\Seguridad\Usuario;
use Illuminate\Database\Seeder;

class CompraSeeder extends Seeder
{
    /**
     * Ejecuta el seeder.
     */
    public function run(): void
    {
        $proveedor =
            Proveedor::where(
                'estado_registro',
                'A'
            )
                ->orderBy(
                    'id_proveedor'
                )
                ->first();

        $sucursal =
            Sucursal::where(
                'estado_registro',
                'A'
            )
                ->orderBy(
                    'id_sucursal'
                )
                ->first();

        $usuario =
            Usuario::where(
                'estado_registro',
                'A'
            )
                ->orderBy(
                    'id_usuario'
                )
                ->first();

        if (
            ! $proveedor
            || ! $sucursal
            || ! $usuario
        ) {

            return;

        }

        Compra::firstOrCreate(

            [
                'codigo_compra' =>
                    'COM-SEED-001'
            ],

            [
                'id_sucursal' =>
                    $sucursal->id_sucursal,

                'id_proveedor' =>
                    $proveedor->id_proveedor,

                'id_usuario_comprador' =>
                    $usuario->id_usuario,

                'estado_compra' =>
                    Compra::BORRADOR,

                'fecha_compra' =>
                    now(),

                'numero_factura' =>
                    null,

                'subtotal' =>
                    0,

                'descuento' =>
                    0,

                'total' =>
                    0,

                'observaciones' =>
                    'Compra inicial generada desde el seeder.',

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