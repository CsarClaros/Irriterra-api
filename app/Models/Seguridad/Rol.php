<?php

namespace App\Models\Seguridad;

use App\Models\Seguridad\Permiso;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rol extends Model
{
    use HasFactory;

    /**
     * Tabla.
     */
    protected $table = 'rol';

    /**
     * Clave primaria.
     */
    protected $primaryKey = 'id_rol';

    /**
     * Tipo de clave.
     */
    protected $keyType = 'int';

    /**
     * Incremental.
     */
    public $incrementing = true;

    /**
     * Campos asignables.
     */
    protected $fillable = [

        'nombre',

        'descripcion',

        'nivel',

        'estado_registro',

        'usuario_creacion',

        'usuario_modificacion'

    ];

    /**
     * Conversión de atributos.
     */
    protected $casts = [

        'id_rol' =>
            'integer',

        'nivel' =>
            'integer',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    /**
     * Permisos activos asignados al rol.
     */
    public function permisos(): BelongsToMany
    {
        return $this->belongsToMany(

            Permiso::class,

            'rol_permiso',

            'id_rol',

            'id_permiso'

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
                'permiso.estado_registro',
                'A'
            )
            ->withTimestamps();
    }

    public const SUPER_ADMINISTRADOR =
        'SuperAdministrador';

    public const ADMINISTRADOR =
        'Administrador';

    public const GERENTE =
        'Gerente';

    public const SUPERVISOR =
        'Supervisor';

    public const VENDEDOR =
        'Vendedor';

    public const ALMACENERO =
        'Almacenero';


    public const NIVEL_SUPER_ADMINISTRADOR =
        100;

    public const NIVEL_ADMINISTRADOR =
        80;

    public const NIVEL_GERENTE =
        60;

    public const NIVEL_SUPERVISOR =
        40;

    public const NIVEL_VENDEDOR =
        20;

    public const NIVEL_ALMACENERO =
        20;
}
