<?php

namespace Database\Seeders\Inventario;

use App\Models\Inventario\MovimientoInventario;
use App\Models\Inventario\StockSucursal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MovimientoInventarioSeeder extends Seeder
{
    /**
     * Ejecuta el seeder.
     */
    public function run(): void
    {
        $stockSucursal = StockSucursal::where(
            'estado_registro',
            'A'
        )
            ->orderBy('id_stock_sucursal')
            ->first();

        if (! $stockSucursal) {

            return;

        }

        DB::transaction(
            function () use ($stockSucursal): void {

                $cantidad = 10;

                $stockAnterior = (float)
                    $stockSucursal->stock_actual;

                $stockResultante =
                    $stockAnterior + $cantidad;

                $movimiento =
                    MovimientoInventario::firstOrCreate(

                        [
                            'codigo_movimiento' =>
                                'MOV-SEED-001'
                        ],

                        [
                            'id_stock_sucursal' =>
                                $stockSucursal
                                    ->id_stock_sucursal,

                            'tipo_movimiento' =>
                                'ENTRADA',

                            'cantidad' => $cantidad,

                            'stock_anterior' =>
                                $stockAnterior,

                            'stock_resultante' =>
                                $stockResultante,

                            'motivo' =>
                                'Carga inicial de inventario',

                            'tipo_referencia' =>
                                'SEEDER',

                            'id_referencia' => null,

                            'fecha_movimiento' => now(),

                            'observaciones' =>
                                'Movimiento inicial generado desde el seeder.',

                            'estado_registro' => 'A',

                            'usuario_creacion' => null,

                            'usuario_modificacion' => null
                        ]

                    );

                if ($movimiento->wasRecentlyCreated) {

                    $stockSucursal->update([

                        'stock_actual' =>
                            $stockResultante

                    ]);

                }

            }
        );
    }
}