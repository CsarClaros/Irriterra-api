<?php

namespace Database\Seeders\Seguridad;

use App\Models\Seguridad\Permiso;
use Illuminate\Database\Seeder;

class PermisoSeeder extends Seeder
{
    /**
     * Ejecuta el seeder.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | CRUD estándar
        |--------------------------------------------------------------------------
        */

        $modulosCrud = [

            'empresa' =>
                'empresas',

            'sucursal' =>
                'sucursales',

            'rol' =>
                'roles',

            'permiso' =>
                'permisos',

            'rol_permiso' =>
                'asignaciones de permisos',

            'usuario' =>
                'usuarios',

            'categoria' =>
                'categorías',

            'producto' =>
                'productos',

            'producto_variante' =>
                'variantes de productos',

            'producto_imagen' =>
                'imágenes de productos',

            'stock' =>
                'registros de stock',

            'precio' =>
                'precios',

            'cliente' =>
                'clientes',

            'proveedor' =>
                'proveedores'

        ];

        $acciones = [

            'ver' =>
                'visualizar',

            'crear' =>
                'registrar',

            'editar' =>
                'modificar',

            'eliminar' =>
                'desactivar'

        ];

        foreach (
            $modulosCrud as
            $modulo => $descripcionModulo
        ) {

            foreach (
                $acciones as
                $accion => $descripcionAccion
            ) {

                $this->registrarPermiso(

                    $modulo . '.' . $accion,

                    'Permite '
                    . $descripcionAccion
                    . ' '
                    . $descripcionModulo
                    . '.'

                );

            }

        }

        /*
        |--------------------------------------------------------------------------
        | Movimiento de inventario
        |--------------------------------------------------------------------------
        */

        $this->registrarPermiso(

            'movimiento.ver',

            'Permite visualizar movimientos de inventario.'

        );

        $this->registrarPermiso(

            'movimiento.crear',

            'Permite registrar movimientos de inventario.'

        );

        $this->registrarPermiso(

            'movimiento.editar',

            'Permite modificar movimientos de inventario.'

        );

        /*
        |--------------------------------------------------------------------------
        | Precio sensible
        |--------------------------------------------------------------------------
        */

        $this->registrarPermiso(

            'precio.costo_compra.ver',

            'Permite visualizar el costo de compra de los productos.'

        );

        /*
        |--------------------------------------------------------------------------
        | Transferencias
        |--------------------------------------------------------------------------
        */

        foreach (
            $acciones as
            $accion => $descripcionAccion
        ) {

            $this->registrarPermiso(

                'transferencia.' . $accion,

                'Permite '
                . $descripcionAccion
                . ' transferencias de inventario.'

            );

        }

        $this->registrarPermiso(

            'transferencia.enviar',

            'Permite enviar transferencias.'

        );

        $this->registrarPermiso(

            'transferencia.completar',

            'Permite completar transferencias.'

        );

        $this->registrarPermiso(

            'transferencia.rechazar',

            'Permite rechazar transferencias.'

        );

        /*
        |--------------------------------------------------------------------------
        | Ventas
        |--------------------------------------------------------------------------
        */

        foreach (
            $acciones as
            $accion => $descripcionAccion
        ) {

            $this->registrarPermiso(

                'venta.' . $accion,

                'Permite '
                . $descripcionAccion
                . ' ventas.'

            );

        }

        $this->registrarPermiso(

            'venta.completar',

            'Permite completar ventas.'

        );

        $this->registrarPermiso(

            'venta.anular',

            'Permite anular ventas.'

        );

        /*
        |--------------------------------------------------------------------------
        | Compras
        |--------------------------------------------------------------------------
        */

        foreach (
            $acciones as
            $accion => $descripcionAccion
        ) {

            $this->registrarPermiso(

                'compra.' . $accion,

                'Permite '
                . $descripcionAccion
                . ' compras.'

            );

        }

        $this->registrarPermiso(

            'compra.confirmar',

            'Permite confirmar compras.'

        );

        $this->registrarPermiso(

            'compra.recibir',

            'Permite recibir compras.'

        );

        $this->registrarPermiso(

            'compra.anular',

            'Permite anular compras.'

        );

        /*
        |--------------------------------------------------------------------------
        | Reportes
        |--------------------------------------------------------------------------
        */

        $this->registrarPermiso(

            'reporte_inventario.ver',

            'Permite consultar reportes de inventario.'

        );

        $this->registrarPermiso(

            'reporte_ventas.ver',

            'Permite consultar reportes de ventas.'

        );

        $this->registrarPermiso(

            'reporte_compras.ver',

            'Permite consultar reportes de compras.'

        );

        $this->registrarPermiso(

            'reporte_transferencias.ver',

            'Permite consultar reportes de transferencias.'

        );
    }

    /**
     * Registra o actualiza un permiso.
     */
    private function registrarPermiso(
        string $nombre,
        string $descripcion
    ): void {

        Permiso::updateOrCreate(

            [
                'nombre' =>
                    $nombre
            ],

            [
                'descripcion' =>
                    $descripcion,

                'estado_registro' =>
                    'A'
            ]

        );
    }
}
