<?php

namespace App\Models\Compras;

use App\Models\Inventario\ProductoVariante;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Inventario\InventarioItem;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompraDetalle extends Model
{
    /**
     * Nombre de la tabla.
     */
    protected $table =
        'compra_detalle';

    /**
     * Clave primaria.
     */
    protected $primaryKey =
        'id_compra_detalle';

    /**
     * Campos asignables.
     */
    protected $fillable = [

        'id_compra',

        'id_producto_variante',

        'cantidad',

        'costo_unitario',

        'descuento',

        'subtotal',

        'costo_unitario_original',

        'descuento_original',

        'subtotal_original',

        'observaciones',

        'estado_registro',

        'usuario_creacion',

        'usuario_modificacion'

    ];

    /**
     * Conversión de atributos.
     */
    protected $casts = [

        'id_compra_detalle' =>
            'integer',

        'id_compra' =>
            'integer',

        'id_producto_variante' =>
            'integer',

        'cantidad' =>
            'decimal:3',

        'costo_unitario' =>
            'decimal:2',

        'descuento' =>
            'decimal:2',

        'subtotal' =>
            'decimal:2',

        'costo_unitario_original' =>
            'decimal:2',

        'descuento_original' =>
            'decimal:2',

        'subtotal_original' =>
            'decimal:2',

        'usuario_creacion' =>
            'integer',

        'usuario_modificacion' =>
            'integer'

    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    /**
     * Compra propietaria del detalle.
     */
    public function compra(): BelongsTo
    {
        return $this->belongsTo(

            Compra::class,

            'id_compra',

            'id_compra'

        );
    }

    /**
     * Variante comprada.
     */
    public function productoVariante(): BelongsTo
    {
        return $this->belongsTo(

            ProductoVariante::class,

            'id_producto_variante',

            'id_producto_variante'

        );
    }

    /**
     * Unidades serializadas originadas
     * mediante este detalle de compra.
     */
    public function itemsOrigen(): HasMany
    {
        return $this->hasMany(

            InventarioItem::class,

            'id_compra_detalle_origen',

            'id_compra_detalle'

        );
    }
}
