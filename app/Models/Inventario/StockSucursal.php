<?php

namespace App\Models\Inventario;

use App\Models\Organizacion\Sucursal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockSucursal extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla.
     */
    protected $table = 'stock_sucursal';

    /**
     * Clave primaria.
     */
    protected $primaryKey = 'id_stock_sucursal';

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

        'id_sucursal',

        'id_producto_variante',

        'stock_actual',

        'stock_minimo',

        'stock_maximo',

        'ubicacion_almacen',

        'observaciones',

        'estado_registro',

        'usuario_creacion',

        'usuario_modificacion'

    ];

    /**
     * Conversión de atributos.
     */
    protected $casts = [

        'id_stock_sucursal' => 'integer',

        'id_sucursal' => 'integer',

        'id_producto_variante' => 'integer',

        'stock_actual' => 'decimal:3',

        'stock_minimo' => 'decimal:3',

        'stock_maximo' => 'decimal:3',

        'usuario_creacion' => 'integer',

        'usuario_modificacion' => 'integer'

    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    /**
     * Sucursal propietaria del stock.
     */
    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(

            Sucursal::class,

            'id_sucursal',

            'id_sucursal'

        );
    }

    /**
     * Variante almacenada en la sucursal.
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
     * Movimientos pertenecientes al stock.
     */
    public function movimientos(): HasMany
    {
        return $this->hasMany(

            MovimientoInventario::class,

            'id_stock_sucursal',

            'id_stock_sucursal'

        )
            ->orderByDesc('fecha_movimiento');
    }
}
