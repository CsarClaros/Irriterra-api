<?php

namespace App\Models\Seguridad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Seguridad\Rol;
use App\Models\Seguridad\Permiso;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolPermiso extends Model
{
    use HasFactory;

    /**
     * Tabla.
     */
    protected $table = 'rol_permiso';

    /**
     * Clave primaria.
     */
    protected $primaryKey = 'id_rol_permiso';

    /**
     * Asignación masiva.
     */
    protected $fillable = [

        'id_rol',

        'id_permiso',

        'estado_registro',

        'usuario_creacion',

        'usuario_modificacion'

    ];

        /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    /**
     * Rol asociado.
     */
    public function rol(): BelongsTo
    {
        return $this->belongsTo(

            Rol::class,

            'id_rol',

            'id_rol'

        );
    }

    /**
     * Permiso asociado.
     */
    public function permiso(): BelongsTo
    {
        return $this->belongsTo(

            Permiso::class,

            'id_permiso',

            'id_permiso'

        );
    }


}