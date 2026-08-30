<?php

namespace App\Models\Transferencias;

use App\Models\Inventario\ProductoVariante;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferenciaInventarioDetalle extends Model
{
    /**
     * Nombre de la tabla.
     */
    protected $table =
        'transferencia_inventario_detalle';

    /**
     * Clave primaria.
     */
    protected $primaryKey =
        'id_transferencia_inventario_detalle';

    /**
     * Campos asignables.
     */
    protected $fillable = [

        'id_transferencia_inventario',

        'id_producto_variante',

        'cantidad',

        'observaciones',

        'estado_registro',

        'usuario_creacion',

        'usuario_modificacion'

    ];

    /**
     * Conversión de atributos.
     */
    protected $casts = [

        'id_transferencia_inventario_detalle' =>
            'integer',

        'id_transferencia_inventario' =>
            'integer',

        'id_producto_variante' =>
            'integer',

        'cantidad' =>
            'decimal:3',

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
     * Transferencia a la que pertenece.
     */
    public function transferenciaInventario(): BelongsTo
    {
        return $this->belongsTo(

            TransferenciaInventario::class,

            'id_transferencia_inventario',

            'id_transferencia_inventario'

        );
    }

    /**
     * Variante que será transferida.
     */
    public function productoVariante(): BelongsTo
    {
        return $this->belongsTo(

            ProductoVariante::class,

            'id_producto_variante',

            'id_producto_variante'

        );
    }
}