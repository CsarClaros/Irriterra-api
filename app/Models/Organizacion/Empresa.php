<?php

namespace App\Models\Organizacion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Empresa extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla.
     *
     * @var string
     */
    protected $table = 'empresa';

    /**
     * Llave primaria.
     *
     * @var string
     */
    protected $primaryKey = 'id_empresa';

    /**
     * Tipo de llave primaria.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Incremento automático.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Campos asignables masivamente.
     *
     * @var array<int,string>
     */
    protected $fillable = [

        'nombre',
        'nit',
        'telefono',
        'correo',
        'direccion',
        'sitio_web',
        'logo',
        'observaciones',
        'estado_registro'

    ];

    /**
     * Conversión automática de tipos.
     *
     * @var array<string,string>
     */
    protected $casts = [

        'created_at' => 'datetime',
        'updated_at' => 'datetime',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    /**
     * Una empresa posee muchas sucursales.
     */

    public function sucursales()
    {
        return $this->hasMany(
            Sucursal::class,
            'id_empresa',
            'id_empresa'
        );
    }
}