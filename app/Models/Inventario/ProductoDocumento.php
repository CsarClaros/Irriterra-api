<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class ProductoDocumento extends Model
{

    use HasFactory;


    protected $table =
        'producto_documento';


    protected $primaryKey =
        'id_producto_documento';


    protected $keyType =
        'int';


    public $incrementing =
        true;


    protected $fillable = [

        'id_producto',

        'tipo',

        'nombre',

        'archivo',

        'es_publico',

        'orden',

        'observaciones',

        'estado_registro'

    ];


    protected $casts = [

        'id_producto_documento' =>
            'integer',

        'id_producto' =>
            'integer',

        'es_publico' =>
            'boolean',

        'orden' =>
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

}
