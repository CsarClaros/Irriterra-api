<?php

namespace App\Models\Inventario;

use App\Models\Compras\CompraDetalle;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventarioItem extends Model
{
    use HasFactory;

    public const DISPONIBLE =
        'DISPONIBLE';

    public const EN_TRANSITO =
        'EN_TRANSITO';

    public const VENDIDO =
        'VENDIDO';

    public const ESTADOS = [

        self::DISPONIBLE,

        self::EN_TRANSITO,

        self::VENDIDO

    ];

    protected $table =
        'inventario_item';

    protected $primaryKey =
        'id_inventario_item';

    protected $keyType =
        'int';

    public $incrementing =
        true;

    protected $fillable = [

        'id_producto_variante',

        'id_stock_sucursal',

        'id_compra_detalle_origen',

        'numero_serie',

        'estado_item',

        'fecha_ingreso',

        'observaciones',

        'estado_registro',

        'usuario_creacion',

        'usuario_modificacion'

    ];

    protected $casts = [

        'id_inventario_item' =>
            'integer',

        'id_producto_variante' =>
            'integer',

        'id_stock_sucursal' =>
            'integer',

        'id_compra_detalle_origen' =>
            'integer',

        'fecha_ingreso' =>
            'datetime',

        'usuario_creacion' =>
            'integer',

        'usuario_modificacion' =>
            'integer'

    ];

    public function productoVariante(): BelongsTo
    {
        return $this->belongsTo(

            ProductoVariante::class,

            'id_producto_variante',

            'id_producto_variante'

        );
    }

    public function stockSucursal(): BelongsTo
    {
        return $this->belongsTo(

            StockSucursal::class,

            'id_stock_sucursal',

            'id_stock_sucursal'

        );
    }

    public function compraDetalleOrigen(): BelongsTo
    {
        return $this->belongsTo(

            CompraDetalle::class,

            'id_compra_detalle_origen',

            'id_compra_detalle'

        );
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(

            MovimientoInventarioItem::class,

            'id_inventario_item',

            'id_inventario_item'

        )
            ->orderBy(
                'id_movimiento_inventario_item'
            );
    }
}
