<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoInventario extends Model
{
    use HasFactory;

    /**
     * Tipos de movimiento.
     */
    public const ENTRADA = 'ENTRADA';

    public const SALIDA = 'SALIDA';

    public const AJUSTE_ENTRADA = 'AJUSTE_ENTRADA';

    public const AJUSTE_SALIDA = 'AJUSTE_SALIDA';

    /**
     * Tipos de movimiento permitidos.
     */
    public const TIPOS = [

        self::ENTRADA,

        self::SALIDA,

        self::AJUSTE_ENTRADA,

        self::AJUSTE_SALIDA

    ];

    /**
     * Nombre de la tabla.
     */
    protected $table = 'movimiento_inventario';

    /**
     * Clave primaria.
     */
    protected $primaryKey =
        'id_movimiento_inventario';

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

        'id_stock_sucursal',

        'codigo_movimiento',

        'tipo_movimiento',

        'cantidad',

        'stock_anterior',

        'stock_resultante',

        'motivo',

        'tipo_referencia',

        'id_referencia',

        'fecha_movimiento',

        'observaciones',

        'estado_registro',

        'usuario_creacion',

        'usuario_modificacion'

    ];

    /**
     * Conversión de atributos.
     */
    protected $casts = [

        'id_movimiento_inventario' => 'integer',

        'id_stock_sucursal' => 'integer',

        'cantidad' => 'decimal:3',

        'stock_anterior' => 'decimal:3',

        'stock_resultante' => 'decimal:3',

        'id_referencia' => 'integer',

        'fecha_movimiento' => 'datetime',

        'usuario_creacion' => 'integer',

        'usuario_modificacion' => 'integer'

    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    /**
     * Registro de stock afectado.
     */
    public function stockSucursal(): BelongsTo
    {
        return $this->belongsTo(

            StockSucursal::class,

            'id_stock_sucursal',

            'id_stock_sucursal'

        );
    }
}