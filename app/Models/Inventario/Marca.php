<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Marca extends Model
{

    use HasFactory;


    protected $table =
        'marca';


    protected $primaryKey =
        'id_marca';


    protected $keyType =
        'int';


    public $incrementing =
        true;


    protected $fillable = [

        'nombre',

        'slug',

        'pais',

        'logo',

        'sitio_web',

        'orden',

        'observaciones',

        'estado_registro'

    ];


    protected $casts = [

        'id_marca' =>
            'integer',

        'orden' =>
            'integer'

    ];


    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function variantes(): HasMany
    {

        return $this->hasMany(

            ProductoVariante::class,

            'id_marca',

            'id_marca'

        );

    }

}
