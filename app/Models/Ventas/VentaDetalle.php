<?php

namespace App\Models\Ventas;

use App\Models\Inventario\ProductoVariante;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VentaDetalle extends Model
{
    /**
     * Nombre de la tabla.
     */
    protected $table = 'venta_detalle';

    /**
     * Clave primaria.
     */
    protected $primaryKey =
        'id_venta_detalle';

    /**
     * Campos asignables.
     */
    protected $fillable = [

        'id_venta',

        'id_producto_variante',

        'cantidad',

        'precio_unitario',

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

        'id_venta_detalle' =>
            'integer',

        'id_venta' =>
            'integer',

        'id_producto_variante' =>
            'integer',

        'cantidad' =>
            'decimal:3',

        'precio_unitario' =>
            'decimal:2',

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
     * Venta propietaria del detalle.
     */
    public function venta(): BelongsTo
    {
        return $this->belongsTo(

            Venta::class,

            'id_venta',

            'id_venta'

        );
    }

    /**
     * Variante vendida.
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