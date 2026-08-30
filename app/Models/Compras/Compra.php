<?php

namespace App\Models\Compras;

use App\Models\Organizacion\Sucursal;
use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Compra extends Model
{
    /**
     * Estados de compra.
     */
    public const BORRADOR = 'BORRADOR';

    public const CONFIRMADA = 'CONFIRMADA';

    public const RECIBIDA = 'RECIBIDA';

    public const ANULADA = 'ANULADA';

    public const ESTADOS = [

        self::BORRADOR,

        self::CONFIRMADA,

        self::RECIBIDA,

        self::ANULADA

    ];

    /**
     * Nombre de la tabla.
     */
    protected $table = 'compra';

    /**
     * Clave primaria.
     */
    protected $primaryKey =
        'id_compra';

    /**
     * Campos asignables.
     */
    protected $fillable = [

        'codigo_compra',

        'id_sucursal',

        'id_proveedor',

        'id_usuario_comprador',

        'estado_compra',

        'fecha_compra',

        'fecha_confirmacion',

        'fecha_recepcion',

        'fecha_anulacion',

        'numero_factura',

        'subtotal',

        'descuento',

        'total',

        'id_usuario_confirmacion',

        'id_usuario_recepcion',

        'id_usuario_anulacion',

        'motivo_anulacion',

        'observaciones',

        'estado_registro',

        'usuario_creacion',

        'usuario_modificacion'

    ];

    /**
     * Conversión de atributos.
     */
    protected $casts = [

        'id_compra' =>
            'integer',

        'id_sucursal' =>
            'integer',

        'id_proveedor' =>
            'integer',

        'id_usuario_comprador' =>
            'integer',

        'id_usuario_confirmacion' =>
            'integer',

        'id_usuario_recepcion' =>
            'integer',

        'id_usuario_anulacion' =>
            'integer',

        'fecha_compra' =>
            'datetime',

        'fecha_confirmacion' =>
            'datetime',

        'fecha_recepcion' =>
            'datetime',

        'fecha_anulacion' =>
            'datetime',

        'subtotal' =>
            'decimal:2',

        'descuento' =>
            'decimal:2',

        'total' =>
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
     * Sucursal que recibirá la compra.
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
     * Proveedor de la compra.
     */
    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(

            Proveedor::class,

            'id_proveedor',

            'id_proveedor'

        );
    }

    /**
     * Usuario comprador.
     */
    public function comprador(): BelongsTo
    {
        return $this->belongsTo(

            Usuario::class,

            'id_usuario_comprador',

            'id_usuario'

        );
    }

    /**
     * Usuario que confirmó la compra.
     */
    public function usuarioConfirmacion(): BelongsTo
    {
        return $this->belongsTo(

            Usuario::class,

            'id_usuario_confirmacion',

            'id_usuario'

        );
    }

    /**
     * Usuario que recibió la compra.
     */
    public function usuarioRecepcion(): BelongsTo
    {
        return $this->belongsTo(

            Usuario::class,

            'id_usuario_recepcion',

            'id_usuario'

        );
    }

    /**
     * Usuario que anuló la compra.
     */
    public function usuarioAnulacion(): BelongsTo
    {
        return $this->belongsTo(

            Usuario::class,

            'id_usuario_anulacion',

            'id_usuario'

        );
    }

    /**
     * Detalles activos.
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(

            CompraDetalle::class,

            'id_compra',

            'id_compra'

        )
            ->where(
                'estado_registro',
                'A'
            );
    }
}