<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductoImagen extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla.
     */
    protected $table = 'producto_imagen';

    /**
     * Clave primaria.
     */
    protected $primaryKey = 'id_producto_imagen';

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

        'ruta_imagen',

        'texto_alternativo',

        'orden',

        'es_principal',

        'observaciones',

        'estado_registro',

        'usuario_creacion',

        'usuario_modificacion'

    ];

    /**
     * Conversión de atributos.
     */
    protected $casts = [

        'id_producto_imagen' => 'integer',

        'id_producto' => 'integer',

        'orden' => 'integer',

        'es_principal' => 'boolean',

        'usuario_creacion' => 'integer',

        'usuario_modificacion' => 'integer'

    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    /**
     * Producto al que pertenece la imagen.
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(

            Producto::class,

            'id_producto',

            'id_producto'

        );
    }
}