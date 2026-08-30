<?php

namespace App\Models\Transferencias;

use App\Models\Organizacion\Sucursal;
use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransferenciaInventario extends Model
{
    /**
     * Estados de la transferencia.
     */
    public const PENDIENTE = 'PENDIENTE';

    public const EN_TRANSITO = 'EN_TRANSITO';

    public const COMPLETADA = 'COMPLETADA';

    public const RECHAZADA = 'RECHAZADA';

    /**
     * Estados permitidos.
     */
    public const ESTADOS = [

        self::PENDIENTE,

        self::EN_TRANSITO,

        self::COMPLETADA,

        self::RECHAZADA

    ];

    /**
     * Nombre de la tabla.
     */
    protected $table =
        'transferencia_inventario';

    /**
     * Clave primaria.
     */
    protected $primaryKey =
        'id_transferencia_inventario';

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

        'codigo_transferencia',

        'id_sucursal_origen',

        'id_sucursal_destino',

        'estado_transferencia',

        'fecha_solicitud',

        'fecha_envio',

        'fecha_recepcion',

        'fecha_rechazo',

        'id_usuario_solicitud',

        'id_usuario_envio',

        'id_usuario_recepcion',

        'id_usuario_rechazo',

        'motivo_rechazo',

        'observaciones',

        'estado_registro',

        'usuario_creacion',

        'usuario_modificacion'

    ];

    /**
     * Conversión de atributos.
     */
    protected $casts = [

        'id_transferencia_inventario' =>
            'integer',

        'id_sucursal_origen' =>
            'integer',

        'id_sucursal_destino' =>
            'integer',

        'fecha_solicitud' =>
            'datetime',

        'fecha_envio' =>
            'datetime',

        'fecha_recepcion' =>
            'datetime',

        'fecha_rechazo' =>
            'datetime',

        'id_usuario_solicitud' =>
            'integer',

        'id_usuario_envio' =>
            'integer',

        'id_usuario_recepcion' =>
            'integer',

        'id_usuario_rechazo' =>
            'integer',

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
     * Sucursal desde donde se envía el inventario.
     */
    public function sucursalOrigen(): BelongsTo
    {
        return $this->belongsTo(

            Sucursal::class,

            'id_sucursal_origen',

            'id_sucursal'

        );
    }

    /**
     * Sucursal que recibirá el inventario.
     */
    public function sucursalDestino(): BelongsTo
    {
        return $this->belongsTo(

            Sucursal::class,

            'id_sucursal_destino',

            'id_sucursal'

        );
    }

    /**
     * Usuario que solicitó la transferencia.
     */
    public function usuarioSolicitud(): BelongsTo
    {
        return $this->belongsTo(

            Usuario::class,

            'id_usuario_solicitud',

            'id_usuario'

        );
    }

    /**
     * Usuario que realizó el envío.
     */
    public function usuarioEnvio(): BelongsTo
    {
        return $this->belongsTo(

            Usuario::class,

            'id_usuario_envio',

            'id_usuario'

        );
    }

    /**
     * Usuario que confirmó la recepción.
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
     * Usuario que rechazó la transferencia.
     */
    public function usuarioRechazo(): BelongsTo
    {
        return $this->belongsTo(

            Usuario::class,

            'id_usuario_rechazo',

            'id_usuario'

        );
    }

    /**
     * Detalles pertenecientes a la transferencia.
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(

            TransferenciaInventarioDetalle::class,

            'id_transferencia_inventario',

            'id_transferencia_inventario'

        )
            ->where(
                'estado_registro',
                'A'
            );
    }
}