<?php

namespace App\Models\Seguridad;

use App\Models\Seguridad\Rol;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permiso extends Model
{
    use HasFactory;

    /**
     * Tabla.
     */
    protected $table = 'permiso';

    /**
     * Clave primaria.
     */
    protected $primaryKey = 'id_permiso';

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

        'estado_registro',

        'usuario_creacion',

        'usuario_modificacion'

    ];

    /**
     * Conversión de atributos.
     */
    protected $casts = [

        'id_permiso' =>
            'integer'

    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    /**
     * Roles activos que poseen este permiso.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(

            Rol::class,

            'rol_permiso',

            'id_permiso',

            'id_rol'

        )
            ->withPivot([

                'id_rol_permiso',

                'estado_registro',

                'usuario_creacion',

                'usuario_modificacion'

            ])
            ->wherePivot(
                'estado_registro',
                'A'
            )
            ->where(
                'rol.estado_registro',
                'A'
            )
            ->withTimestamps();
    }
}
