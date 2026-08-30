<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla.
     */
    protected $table = 'producto';

    /**
     * Clave primaria.
     */
    protected $primaryKey = 'id_producto';

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

        'id_categoria',

        'nombre',

        'marca',

        'modelo',

        'descripcion',

        'catalogo_pdf',

        'observaciones',

        'estado_registro',

        'usuario_creacion',

        'usuario_modificacion'

    ];

    /**
     * Conversión de atributos.
     */
    protected $casts = [

        'id_producto' => 'integer',

        'id_categoria' => 'integer',

        'usuario_creacion' => 'integer',

        'usuario_modificacion' => 'integer'

    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    /**
     * Categoría a la que pertenece el producto.
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(

            Categoria::class,

            'id_categoria',

            'id_categoria'

        );
    }

    /**
     * Variantes pertenecientes al producto.
     */
    public function variantes(): HasMany
    {
        return $this->hasMany(

            ProductoVariante::class,

            'id_producto',

            'id_producto'

        );
    }

    /**
     * Imágenes pertenecientes al producto.
     */
    public function imagenes(): HasMany
    {
        return $this->hasMany(

            ProductoImagen::class,

            'id_producto',

            'id_producto'

        )
            ->orderBy('orden');
    }
}
