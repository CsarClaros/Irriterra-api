<?php

namespace App\Models\Organizacion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Inventario\StockSucursal;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Transferencias\TransferenciaInventario;
use App\Models\Ventas\Venta;
use App\Models\Compras\Compra;

class Sucursal extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla.
     */
    protected $table = 'sucursal';

    /**
     * Llave primaria.
     */
    protected $primaryKey = 'id_sucursal';

    /**
     * Tipo de llave.
     */
    protected $keyType = 'int';

    /**
     * Autoincremental.
     */
    public $incrementing = true;

    /**
     * Asignación masiva.
     */
    protected $fillable = [

        'id_empresa',

        'codigo',
        'nombre',

        'departamento',
        'ciudad',
        'direccion',

        'telefono',
        'correo',

        'latitud',
        'longitud',
        'url_maps',

        'observaciones',

        'estado_registro'

    ];

    /**
     * Conversión automática.
     */
    protected $casts = [

        'id_sucursal' =>
            'integer',

        'id_empresa' =>
            'integer',

        'latitud' =>
            'decimal:8',

        'longitud' =>
            'decimal:8',

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
     * Una sucursal pertenece a una empresa.
     */
    public function empresa()
    {
        return $this->belongsTo(
            Empresa::class,
            'id_empresa',
            'id_empresa'
        );
    }

    /**
     * Registros de stock pertenecientes a la sucursal.
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(

            StockSucursal::class,

            'id_sucursal',

            'id_sucursal'

        );
    }

    /**
     * Transferencias enviadas por la sucursal.
     */
    public function transferenciasOrigen(): HasMany
    {
        return $this->hasMany(

            TransferenciaInventario::class,

            'id_sucursal_origen',

            'id_sucursal'

        );
    }

    /**
     * Transferencias recibidas por la sucursal.
     */
    public function transferenciasDestino(): HasMany
    {
        return $this->hasMany(

            TransferenciaInventario::class,

            'id_sucursal_destino',

            'id_sucursal'

        );
    }

    /**
     * Ventas realizadas en la sucursal.
     */
    public function ventas(): HasMany
    {
        return $this->hasMany(

            Venta::class,

            'id_sucursal',

            'id_sucursal'

        );
    }

    /**
     * Compras destinadas a la sucursal.
     */
    public function compras(): HasMany
    {
        return $this->hasMany(

            Compra::class,

            'id_sucursal',

            'id_sucursal'

        );
    }
}
