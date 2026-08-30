<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla.
     */
    protected $table = 'categoria';

    /**
     * Clave primaria.
     */
    protected $primaryKey = 'id_categoria';

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

        'nombre',

        'descripcion',

        'observaciones',

        'estado_registro'

    ];

    /**
     * Conversión de atributos.
     */
    protected $casts = [

        'id_categoria' => 'integer'

    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    /**
     * Productos pertenecientes a la categoría.
     */
    public function productos(): HasMany
    {
        return $this->hasMany(

            Producto::class,

            'id_categoria',

            'id_categoria'

        );
    }
}