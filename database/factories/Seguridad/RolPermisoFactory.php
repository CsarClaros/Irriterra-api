<?php

namespace Database\Factories\Seguridad;

use App\Models\Seguridad\Rol;
use App\Models\Seguridad\Permiso;
use App\Models\Seguridad\RolPermiso;
use Illuminate\Database\Eloquent\Factories\Factory;

class RolPermisoFactory extends Factory
{
    /**
     * Modelo asociado.
     */
    protected $model = RolPermiso::class;

    /**
     * Estado por defecto.
     */
    public function definition(): array
    {
        return [

            'id_rol' => Rol::inRandomOrder()->value('id_rol'),

            'id_permiso' => Permiso::inRandomOrder()->value('id_permiso'),

            'estado_registro' => 'A',

            'usuario_creacion' => null,

            'usuario_modificacion' => null

        ];
    }
}