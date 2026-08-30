<?php

namespace App\Models\Compras;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    /**
     * Tipos de proveedor.
     */
    public const PERSONA = 'PERSONA';

    public const EMPRESA = 'EMPRESA';

    public const TIPOS = [

        self::PERSONA,

        self::EMPRESA

    ];

    /**
     * Nombre de la tabla.
     */
    protected $table = 'proveedor';

    /**
     * Clave primaria.
     */
    protected $primaryKey =
        'id_proveedor';

    /**
     * Campos asignables.
     */
    protected $fillable = [

        'tipo_proveedor',

        'nombre_razon_social',

        'tipo_documento',

        'numero_documento',

        'nombre_contacto',

        'telefono',

        'correo',

        'direccion',

        'ciudad',

        'departamento',

        'observaciones',

        'estado_registro',

        'usuario_creacion',

        'usuario_modificacion'

    ];

    /**
     * Conversión de atributos.
     */
    protected $casts = [

        'id_proveedor' =>
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
     * Compras realizadas al proveedor.
     */
    public function compras(): HasMany
    {
        return $this->hasMany(

            Compra::class,

            'id_proveedor',

            'id_proveedor'

        );
    }
}