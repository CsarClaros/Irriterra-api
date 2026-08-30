<?php

namespace App\Models\Compras;

use App\Models\Inventario\ProductoVariante;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}