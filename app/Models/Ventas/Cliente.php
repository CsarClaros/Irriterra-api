<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    /**
     * Tipos de cliente.
     */
    public const PERSONA = 'PERSONA';

    public const EMPRESA = 'EMPRESA';

    public const CONSUMIDOR_FINAL =
        'CONSUMIDOR_FINAL';

    public const TIPOS = [

        self::PERSONA,

        self::EMPRESA,

        self::CONSUMIDOR_FINAL

    ];

    /**
     * Nombre de la tabla.
     */
    protected $table = 'cliente';

    /**
     * Clave primaria.
     */
    protected $primaryKey = 'id_cliente';

    /**
     * Campos asignables.
     */
    protected $fillable = [

        'tipo_cliente',

        'nombre_razon_social',

        'tipo_documento',

        'numero_documento',

        'telefono',

        'correo',

        'direccion',

        'observaciones',

        'estado_registro',

        'usuario_creacion',

        'usuario_modificacion'

    ];

    /**
     * Conversión de atributos.
     */
    protected $casts = [

        'id_cliente' =>
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
     * Ventas pertenecientes al cliente.
     */
    public function ventas(): HasMany
    {
        return $this->hasMany(

            Venta::class,

            'id_cliente',

            'id_cliente'

        );
    }
}