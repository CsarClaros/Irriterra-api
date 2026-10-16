<?php

namespace App\Models\Inventario;

use App\Models\Compras\CompraDetalle;
use App\Models\Transferencias\TransferenciaInventarioDetalle;
use App\Models\Ventas\VentaDetalle;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductoVariante extends Model
{
    use HasFactory;

    public const SIN_CONTROL =
        'SIN_CONTROL';

    public const CANTIDAD =
        'CANTIDAD';

    public const SERIALIZADO =
        'SERIALIZADO';

    public const TIPOS_CONTROL_INVENTARIO = [

        self::SIN_CONTROL,

        self::CANTIDAD,

        self::SERIALIZADO

    ];

    protected $table =
        'producto_variante';

    protected $primaryKey =
        'id_producto_variante';

    protected $keyType =
        'int';

    public $incrementing =
        true;

    protected $fillable = [

        'id_producto',

        'id_marca',

        'nombre',

        'sku',

        'codigo_comercial',

        'unidad_medida',

        'tipo_control_inventario',

        'descripcion',

        'observaciones',

        'estado_registro',

        'usuario_creacion',

        'usuario_modificacion'

    ];

    protected $casts = [

        'id_producto_variante' =>
            'integer',

        'id_producto' =>
            'integer',

        'id_marca' =>
            'integer',

        'usuario_creacion' =>
            'integer',

        'usuario_modificacion' =>
            'integer'

    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(

            Producto::class,

            'id_producto',

            'id_producto'

        );
    }

    public function marca(): BelongsTo
    {
        return $this->belongsTo(

            Marca::class,

            'id_marca',

            'id_marca'

        );
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(

            StockSucursal::class,

            'id_producto_variante',

            'id_producto_variante'

        );
    }

    public function precio(): HasOne
    {
        return $this->hasOne(

            PrecioProductoVariante::class,

            'id_producto_variante',

            'id_producto_variante'

        );
    }

    public function transferenciaDetalles(): HasMany
    {
        return $this->hasMany(

            TransferenciaInventarioDetalle::class,

            'id_producto_variante',

            'id_producto_variante'

        );
    }

    public function ventaDetalles(): HasMany
    {
        return $this->hasMany(

            VentaDetalle::class,

            'id_producto_variante',

            'id_producto_variante'

        );
    }

    public function compraDetalles(): HasMany
    {
        return $this->hasMany(

            CompraDetalle::class,

            'id_producto_variante',

            'id_producto_variante'

        );
    }

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

    public function items(): HasMany
    {
        return $this->hasMany(

            InventarioItem::class,

            'id_producto_variante',

            'id_producto_variante'

        );
    }
}
