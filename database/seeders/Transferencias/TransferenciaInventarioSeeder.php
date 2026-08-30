<?php

namespace Database\Seeders\Transferencias;

use App\Models\Inventario\ProductoVariante;
use App\Models\Organizacion\Sucursal;
use App\Models\Transferencias\TransferenciaInventario;
use App\Models\Transferencias\TransferenciaInventarioDetalle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransferenciaInventarioSeeder extends Seeder
{
    /**
     * Ejecuta el seeder.
     */
    public function run(): void
    {
        $sucursales = Sucursal::where(
            'estado_registro',
            'A'
        )
            ->orderBy('id_sucursal')
            ->take(2)
            ->get();

        $productoVariante =
            ProductoVariante::where(
                'estado_registro',
                'A'
            )
                ->orderBy(
                    'id_producto_variante'
                )
                ->first();

        if (
            $sucursales->count() < 2
            || ! $productoVariante
        ) {

            return;

        }

        DB::transaction(
            function () use (
                $sucursales,
                $productoVariante
            ): void {

                $transferencia =
                    TransferenciaInventario::firstOrCreate(

                        [
                            'codigo_transferencia' =>
                                'TRF-SEED-001'
                        ],

                        [
                            'id_sucursal_origen' =>
                                $sucursales[0]
                                    ->id_sucursal,

                            'id_sucursal_destino' =>
                                $sucursales[1]
                                    ->id_sucursal,

                            'estado_transferencia' =>
                                TransferenciaInventario::PENDIENTE,

                            'fecha_solicitud' =>
                                now(),

                            'observaciones' =>
                                'Transferencia inicial generada desde el seeder.',

                            'estado_registro' =>
                                'A',

                            'usuario_creacion' =>
                                null,

                            'usuario_modificacion' =>
                                null
                        ]

                    );

                TransferenciaInventarioDetalle::updateOrCreate(

                    [
                        'id_transferencia_inventario' =>
                            $transferencia
                                ->id_transferencia_inventario,

                        'id_producto_variante' =>
                            $productoVariante
                                ->id_producto_variante
                    ],

                    [
                        'cantidad' => 1,

                        'observaciones' =>
                            'Detalle inicial de la transferencia.',

                        'estado_registro' =>
                            'A',

                        'usuario_creacion' =>
                            null,

                        'usuario_modificacion' =>
                            null
                    ]

                );

            }
        );
    }
}