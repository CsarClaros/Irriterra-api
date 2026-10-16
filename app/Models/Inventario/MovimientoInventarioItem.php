<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoInventarioItem extends Model
{
    use HasFactory;

    protected $table =
        'movimiento_inventario_item';

    protected $primaryKey =
        'id_movimiento_inventario_item';

    protected $keyType =
        'int';

    public $incrementing =
        true;

    protected $fillable = [

        'id_movimiento_inventario',

        'id_inventario_item',

        'estado_anterior',

        'estado_resultante',

        'id_stock_sucursal_anterior',

        'id_stock_sucursal_resultante',

        'observaciones',

        'estado_registro',

        'usuario_creacion'

    ];

    protected $casts = [

        'id_movimiento_inventario_item' =>
            'integer',

        'id_movimiento_inventario' =>
            'integer',

        'id_inventario_item' =>
            'integer',

        'id_stock_sucursal_anterior' =>
            'integer',

        'id_stock_sucursal_resultante' =>
            'integer',

        'usuario_creacion' =>
            'integer'

    ];

    public function movimientoInventario(): BelongsTo
    {
        return $this->belongsTo(

            MovimientoInventario::class,

            'id_movimiento_inventario',

            'id_movimiento_inventario'

        );
    }

    public function inventarioItem(): BelongsTo
    {
        return $this->belongsTo(

            InventarioItem::class,

            'id_inventario_item',

            'id_inventario_item'

        );
    }

    public function stockSucursalAnterior(): BelongsTo
    {
        return $this->belongsTo(

            StockSucursal::class,

            'id_stock_sucursal_anterior',

            'id_stock_sucursal'

        );
    }

    public function stockSucursalResultante(): BelongsTo
    {
        return $this->belongsTo(

            StockSucursal::class,

            'id_stock_sucursal_resultante',

            'id_stock_sucursal'

        );
    }
}
