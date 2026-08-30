<?php

namespace App\Models\Ventas;

use App\Models\Organizacion\Sucursal;
use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venta extends Model
{
    /**
     * Estados de venta.
     */
    public const BORRADOR = 'BORRADOR';

    public const COMPLETADA = 'COMPLETADA';

    public const ANULADA = 'ANULADA';

    public const ESTADOS = [

        self::BORRADOR,

        self::COMPLETADA,

        self::ANULADA

    ];

    /**
     * Métodos de pago.
     */
    public const EFECTIVO = 'EFECTIVO';

    public const TRANSFERENCIA = 'TRANSFERENCIA';

    public const QR = 'QR';

    public const TARJETA = 'TARJETA';

    public const METODOS_PAGO = [

        self::EFECTIVO,

        self::TRANSFERENCIA,

        self::QR,

        self::TARJETA

    ];

    /**
     * Nombre de la tabla.
     */
    protected $table = 'venta';

    /**
     * Clave primaria.
     */
    protected $primaryKey = 'id_venta';

    /**
     * Campos asignables.
     */
    protected $fillable = [

        'codigo_venta',

        'id_sucursal',

        'id_cliente',

        'id_usuario_vendedor',

        'estado_venta',

        'fecha_venta',

        'fecha_pago',

        'fecha_anulacion',

        'subtotal',

        'descuento',

        'total',

        'monto_pagado',

        'metodo_pago',

        'motivo_anulacion',

        'id_usuario_anulacion',

        'observaciones',

        'estado_registro',

        'usuario_creacion',

        'usuario_modificacion'

    ];

    /**
     * Conversión de atributos.
     */
    protected $casts = [

        'id_venta' =>
            'integer',

        'id_sucursal' =>
            'integer',

        'id_cliente' =>
            'integer',

        'id_usuario_vendedor' =>
            'integer',

        'id_usuario_anulacion' =>
            'integer',

        'fecha_venta' =>
            'datetime',

        'fecha_pago' =>
            'datetime',

        'fecha_anulacion' =>
            'datetime',

        'subtotal' =>
            'decimal:2',

        'descuento' =>
            'decimal:2',

        'total' =>
            'decimal:2',

        'monto_pagado' =>
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
     * Sucursal donde se realizó la venta.
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
     * Cliente de la venta.
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(

            Cliente::class,

            'id_cliente',

            'id_cliente'

        );
    }

    /**
     * Usuario vendedor.
     */
    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(

            Usuario::class,

            'id_usuario_vendedor',

            'id_usuario'

        );
    }

    /**
     * Usuario que anuló la venta.
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
     * Detalles activos de la venta.
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(

            VentaDetalle::class,

            'id_venta',

            'id_venta'

        )
            ->where(
                'estado_registro',
                'A'
            );
    }
}