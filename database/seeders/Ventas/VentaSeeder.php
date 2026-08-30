<?php

namespace Database\Seeders\Ventas;

use App\Models\Organizacion\Sucursal;
use App\Models\Seguridad\Usuario;
use App\Models\Ventas\Cliente;
use App\Models\Ventas\Venta;
use Illuminate\Database\Seeder;

class VentaSeeder extends Seeder
{
    /**
     * Ejecuta el seeder.
     */
    public function run(): void
    {
        $sucursal = Sucursal::where(
            'estado_registro',
            'A'
        )
            ->orderBy('id_sucursal')
            ->first();

        $cliente = Cliente::where(
            'estado_registro',
            'A'
        )
            ->orderBy('id_cliente')
            ->first();

        $usuario = Usuario::where(
            'estado_registro',
            'A'
        )
            ->orderBy('id_usuario')
            ->first();

        if (
            ! $sucursal
            || ! $cliente
            || ! $usuario
        ) {

            return;

        }

        Venta::firstOrCreate(

            [
                'codigo_venta' =>
                    'VEN-SEED-001'
            ],

            [
                'id_sucursal' =>
                    $sucursal->id_sucursal,

                'id_cliente' =>
                    $cliente->id_cliente,

                'id_usuario_vendedor' =>
                    $usuario->id_usuario,

                'estado_venta' =>
                    Venta::BORRADOR,

                'fecha_venta' =>
                    now(),

                'subtotal' =>
                    0,

                'descuento' =>
                    0,

                'total' =>
                    0,

                'monto_pagado' =>
                    0,

                'metodo_pago' =>
                    null,

                'observaciones' =>
                    'Venta inicial generada desde el seeder.',

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