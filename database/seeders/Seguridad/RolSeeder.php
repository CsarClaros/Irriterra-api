<?php

namespace Database\Seeders\Seguridad;

use App\Models\Seguridad\Rol;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    /**
     * Ejecuta el seeder.
     */
    public function run(): void
    {
        $roles = [

            [
                'nombre' =>
                    Rol::SUPER_ADMINISTRADOR,

                'descripcion' =>
                    'Acceso técnico completo y protegido al sistema.',

                'nivel' =>
                    Rol::NIVEL_SUPER_ADMINISTRADOR
            ],

            [
                'nombre' =>
                    Rol::ADMINISTRADOR,

                'descripcion' =>
                    'Administración completa del negocio.',

                'nivel' =>
                    Rol::NIVEL_ADMINISTRADOR
            ],

            [
                'nombre' =>
                    Rol::GERENTE,

                'descripcion' =>
                    'Administración operativa y reportes.',

                'nivel' =>
                    Rol::NIVEL_GERENTE
            ],

            [
                'nombre' =>
                    Rol::SUPERVISOR,

                'descripcion' =>
                    'Supervisión de procesos.',

                'nivel' =>
                    Rol::NIVEL_SUPERVISOR
            ],

            [
                'nombre' =>
                    Rol::VENDEDOR,

                'descripcion' =>
                    'Gestión de ventas.',

                'nivel' =>
                    Rol::NIVEL_VENDEDOR
            ],

            [
                'nombre' =>
                    Rol::ALMACENERO,

                'descripcion' =>
                    'Gestión de inventario.',

                'nivel' =>
                    Rol::NIVEL_ALMACENERO
            ]

        ];
        foreach ($roles as $rol) {

            Rol::updateOrCreate(

                [
                    'nombre' => $rol['nombre']
                ],

                [
                    'descripcion' => $rol['descripcion'],

                    'nivel' => $rol['nivel'],

                    'estado_registro' => 'A'
                ],


            );

        }
    }
}
