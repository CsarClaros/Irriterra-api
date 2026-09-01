<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Transferencias\TransferenciaInventarioDetalle;
use App\Models\Ventas\VentaDetalle;
use App\Models\Compras\CompraDetalle;


class ProductoVariante extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla.
     */
    protected $table = 'producto_variante';

    /**
     * Clave primaria.
     */
    protected $primaryKey = 'id_producto_variante';

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

        'id_producto',

        'nombre',

        'sku',

        'codigo_comercial',

        'unidad_medida',

        'descripcion',

        'observaciones',

        'estado_registro',

        'usuario_creacion',

        'usuario_modificacion'

    ];

    /**
     * Conversión de atributos.
     */
    protected $casts = [

        'id_producto_variante' => 'integer',

        'id_producto' => 'integer',

        'usuario_creacion' => 'integer',

        'usuario_modificacion' => 'integer'

    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    /**
     * Producto al que pertenece la variante.
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(

            Producto::class,

            'id_producto',

            'id_producto'

        );
    }

    /**
     * Existencias de la variante por sucursal.
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(

            StockSucursal::class,

            'id_producto_variante',

            'id_producto_variante'

        );
    }

    /**
     * Configuración actual de precios.
     */
    public function precio(): HasOne
    {
        return $this->hasOne(

            PrecioProductoVariante::class,

            'id_producto_variante',

            'id_producto_variante'

        );
    }

    /**
     * Detalles de transferencia asociados a la variante.
     */
    public function transferenciaDetalles(): HasMany
    {
        return $this->hasMany(

            TransferenciaInventarioDetalle::class,

            'id_producto_variante',

            'id_producto_variante'

        );
    }


    /**
     * Detalles de venta asociados a la variante.
     */
    public function ventaDetalles(): HasMany
    {
        return $this->hasMany(

            VentaDetalle::class,

            'id_producto_variante',

            'id_producto_variante'

        );
    }

    /**
     * Detalles de compra asociados.
     */
    public function compraDetalles(): HasMany
    {
        return $this->hasMany(

            CompraDetalle::class,

            'id_producto_variante',

            'id_producto_variante'

        );
    }

    /**
     * Imágenes específicas de la variante.
     */
    public function imagenes(): HasMany
    {
        return $this->hasMany(

            ProductoImagen::class,

            'id_producto_variante',

            'id_producto_variante'

        )
            ->where(
                'estado_registro',
                'A'
            )
            ->orderByDesc(
                'es_principal'
            )
            ->orderBy(
                'orden'
            );
    }
}
