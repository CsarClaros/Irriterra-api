<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrecioProductoVariante extends Model
{
    /**
     * Nombre de la tabla.
     */
    protected $table =
        'precio_producto_variante';

    /**
     * Clave primaria.
     */
    protected $primaryKey =
        'id_precio_producto_variante';

    /**
     * Tipo de clave.
     */
    protected $keyType = 'int';

    /**
     * Clave incremental.
     */
    public $incrementing = true;

    /**
     * Campos asignables.
     */
    protected $fillable = [

        'id_producto_variante',

        'costo_compra',

        'precio_minimo',

        'precio_venta',

        'fecha_vigencia',

        'observaciones',

        'estado_registro',

        'usuario_creacion',

        'usuario_modificacion'

    ];

    /**
     * Conversión de atributos.
     */
    protected $casts = [

        'id_precio_producto_variante' =>
            'integer',

        'id_producto_variante' =>
            'integer',

        'costo_compra' =>
            'decimal:2',

        'precio_minimo' =>
            'decimal:2',

        'precio_venta' =>
            'decimal:2',

        'fecha_vigencia' =>
            'datetime',

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
     * Variante propietaria de los precios.
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