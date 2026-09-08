<?php

namespace Database\Seeders\Seguridad;

use App\Models\Seguridad\Permiso;
use App\Models\Seguridad\Rol;
use App\Models\Seguridad\RolPermiso;
use Illuminate\Database\Seeder;
use RuntimeException;

class RolPermisoSeeder extends Seeder
{
    /**
     * Ejecuta el seeder.
     */
    public function run(): void
    {
        $permisos =
            Permiso::where(
                'estado_registro',
                'A'
            )
                ->get()
                ->keyBy(
                    'nombre'
                );
        /*
         |--------------------------------------------------------------------------
         | Super Administrador
         |--------------------------------------------------------------------------
         |
         | Todos los permisos existentes.
         |
         */


        $superAdministrador =
            $permisos
                ->keys()
                ->all();
        /*
         |--------------------------------------------------------------------------
         | Administrador
         |---------
        / control de todos los permisos del negocio
         |
         */


        $administrador = [

            ...$this->crud(
                'empresa'
            ),

            ...$this->crud(
                'sucursal'
            ),

            'rol.ver',

            ...$this->crud(
                'usuario'
            ),

            ...$this->crud(
                'categoria'
            ),

            ...$this->crud(
                'marca'
            ),

            ...$this->crud(
                'producto'
            ),

            ...$this->crud(
                'producto_variante'
            ),

            ...$this->crud(
                'producto_imagen'
            ),

            ...$this->crud(
                'producto_documento'
            ),

            ...$this->crud(
                'stock'
            ),

            'movimiento.ver',

            'movimiento.crear',

            'movimiento.editar',

            ...$this->crud(
                'precio'
            ),

            'precio.costo_compra.ver',

            ...$this->crud(
                'transferencia'
            ),

            'transferencia.enviar',

            'transferencia.completar',

            'transferencia.rechazar',

            ...$this->crud(
                'cliente'
            ),

            ...$this->crud(
                'venta'
            ),

            'venta.completar',

            'venta.anular',

            ...$this->crud(
                'proveedor'
            ),

            ...$this->crud(
                'compra'
            ),

            'compra.confirmar',

            'compra.recibir',

            'compra.anular',

            'reporte_inventario.ver',

            'reporte_ventas.ver',

            'reporte_compras.ver',

            'reporte_transferencias.ver'

        ];

        /*
|--------------------------------------------------------------------------
| Gerente
|--------------------------------------------------------------------------
|
| Gestión operativa general.
| No administra roles ni permisos estructurales.
|
*/

        $gerente = [

            ...$this->crud('empresa'),

            ...$this->crud('sucursal'),

            'rol.ver',

            ...$this->crud('usuario'),

            ...$this->crud('categoria'),

            ...$this->crud('producto'),

            ...$this->crud(
                'producto_variante'
            ),

            ...$this->crud(
                'producto_imagen'
            ),

            ...$this->crud(
                'producto_documento'
            ),

            ...$this->crud('stock'),

            'movimiento.ver',

            'movimiento.crear',

            'movimiento.editar',

            ...$this->crud('precio'),

            'precio.costo_compra.ver',

            ...$this->crud(
                'transferencia'
            ),

            'transferencia.enviar',

            'transferencia.completar',

            'transferencia.rechazar',

            ...$this->crud('cliente'),

            ...$this->crud('venta'),

            'venta.completar',

            'venta.anular',

            ...$this->crud('proveedor'),

            'reporte_inventario.ver',

            'reporte_ventas.ver',

            'reporte_transferencias.ver',

            'compra.ver',
            'compra.crear',
            'compra.editar',

            'compra.confirmar',

            'compra.recibir',

            'compra.anular',

            'reporte_compras.ver',


        ];

        /*
        |--------------------------------------------------------------------------
        | Supervisor
        |--------------------------------------------------------------------------
        */

        $supervisor = [

            'sucursal.ver',

            'categoria.ver',

            'marca.ver',

            'producto.ver',

            'producto_variante.ver',

            'producto_imagen.ver',

            'stock.ver',
            'stock.crear',
            'stock.editar',

            'movimiento.ver',
            'movimiento.crear',
            'movimiento.editar',

            'precio.ver',

            ...$this->crud('cliente'),

            ...$this->crud('venta'),

            'venta.completar',
            'venta.anular',

            'proveedor.ver',

            'reporte_inventario.ver',
            'reporte_ventas.ver'

        ];

        /*
        |--------------------------------------------------------------------------
        | Vendedor
        |--------------------------------------------------------------------------
        */

        $vendedor = [

            'sucursal.ver',

            'categoria.ver',

            'marca.ver',

            'producto.ver',

            'producto_variante.ver',

            'producto_imagen.ver',

            'stock.ver',

            'precio.ver',

            'cliente.ver',
            'cliente.crear',
            'cliente.editar',

            ...$this->crud('venta'),

            'venta.completar',

            'venta.anular',

            'reporte_ventas.ver'

        ];

        /*
        |--------------------------------------------------------------------------
        | Almacenero
        |--------------------------------------------------------------------------
        */

        $almacenero = [

            'sucursal.ver',

            'categoria.ver',

            'marca.ver',

            'producto.ver',

            'producto_variante.ver',

            'producto_imagen.ver',

            'stock.ver',

            'movimiento.ver',

            'precio.ver',

            ...$this->crud(
                'transferencia'
            ),

            'transferencia.enviar',

            'transferencia.completar',

            'transferencia.rechazar',

            'reporte_inventario.ver',

            'reporte_transferencias.ver'

        ];

        /*
        |--------------------------------------------------------------------------
        | Matriz
        |--------------------------------------------------------------------------
        */

        $matriz = [

            Rol::SUPER_ADMINISTRADOR =>
                $superAdministrador,

            Rol::ADMINISTRADOR =>
                $administrador,

            Rol::GERENTE =>
                $gerente,

            Rol::SUPERVISOR =>
                $supervisor,

            Rol::VENDEDOR =>
                $vendedor,

            Rol::ALMACENERO =>
                $almacenero

        ];

        foreach (
            $matriz as
            $nombreRol => $nombresPermisos
        ) {

            $rol =
                Rol::where(
                    'nombre',
                    $nombreRol
                )
                    ->where(
                        'estado_registro',
                        'A'
                    )
                    ->first();

            if (
                !$rol
            ) {

                throw new RuntimeException(
                    'No se encontró el rol requerido: '
                    . $nombreRol
                );

            }

            /*
             * Primero se desactivan las asignaciones
             * existentes para que el seeder represente
             * exactamente la matriz definida.
             */
            RolPermiso::where(
                'id_rol',
                $rol->id_rol
            )
                ->update([

                    'estado_registro' =>
                        'I'

                ]);

            foreach (
                array_unique(
                    $nombresPermisos
                )
                as $nombrePermiso
            ) {

                $permiso =
                    $permisos->get(
                        $nombrePermiso
                    );

                if (
                    !$permiso
                ) {

                    throw new RuntimeException(
                        'No se encontró el permiso requerido: '
                        . $nombrePermiso
                        . ' para el rol '
                        . $nombreRol
                        . '.'
                    );

                }

                RolPermiso::updateOrCreate(

                    [
                        'id_rol' =>
                            $rol->id_rol,

                        'id_permiso' =>
                            $permiso->id_permiso
                    ],

                    [
                        'estado_registro' =>
                            'A'
                    ]

                );

            }
        }
    }

    /**
     * Permisos CRUD estándar.
     */
    private function crud(
        string $modulo
    ): array
    {

        return [

            $modulo . '.ver',

            $modulo . '.crear',

            $modulo . '.editar',

            $modulo . '.eliminar'

        ];
    }
}
