<?php

namespace App\Models\Seguridad;

use App\Models\Compras\Compra;
use App\Models\Organizacion\Sucursal;
use App\Models\Ventas\Venta;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

class Usuario extends Authenticatable
{
    use HasApiTokens;

    /**
     * Tabla.
     */
    protected $table = 'usuario';

    /**
     * Clave primaria.
     */
    protected $primaryKey = 'id_usuario';

    /**
     * Campos asignables.
     */
    protected $fillable = [

        'id_rol',

        'id_sucursal',

        'ci',

        'usuario',

        'password',

        'nombre',

        'apellido_paterno',

        'apellido_materno',

        'correo',

        'telefono',

        'direccion',

        'foto',

        'ultimo_acceso',

        'intentos_fallidos',

        'bloqueado_hasta',

        'estado_registro',

    ];

    /**
     * Atributos ocultos.
     */
    protected $hidden = [

        'password'

    ];

    /**
     * Conversión de atributos.
     */
    protected $casts = [

        'id_usuario' => 'integer',

        'id_rol' => 'integer',

        'id_sucursal' => 'integer',

        'ultimo_acceso' => 'datetime',

        'bloqueado_hasta' => 'datetime',

        'intentos_fallidos' => 'integer'

    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    /**
     * Rol principal del usuario.
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
     * Sucursal asignada.
     */
    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(

            Sucursal::class,

            'id_sucursal',

            'id_sucursal'

        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accesorios
    |--------------------------------------------------------------------------
    */

    /**
     * Nombre completo.
     */
    public function getNombreCompletoAttribute(): string
    {
        return trim(

            $this->nombre . ' ' .
                $this->apellido_paterno . ' ' .
                ($this->apellido_materno ?? '')

        );
    }


    /**
     * Ventas realizadas por el usuario.
     */
    public function ventas(): HasMany
    {
        return $this->hasMany(

            Venta::class,

            'id_usuario_vendedor',

            'id_usuario'

        );
    }

    /**
     * Compras realizadas por el usuario.
     */
    public function compras(): HasMany
    {
        return $this->hasMany(

            Compra::class,

            'id_usuario_comprador',

            'id_usuario'

        );
    }

    /**
     * Verifica si el usuario posee un permiso.
     */
    public function tienePermiso(
        string $nombrePermiso
    ): bool {

        $rol =
            $this->rol;

        if (! $rol) {

            return false;

        }

        if (
            $rol->estado_registro
            !== 'A'
        ) {

            return false;

        }

        return $rol
            ->permisos()
            ->where(
                'permiso.nombre',
                $nombrePermiso
            )
            ->exists();
    }
}
