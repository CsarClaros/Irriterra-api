<?php

// use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API ERP IRRITERRA
|--------------------------------------------------------------------------
*/

// require __DIR__.'/api/organizacion.php';

use App\Http\Controllers\Compras\CompraController;
use App\Http\Controllers\Compras\ProveedorController;
use App\Http\Controllers\Inventario\CategoriaController;
use App\Http\Controllers\Inventario\MarcaController;
use App\Http\Controllers\Inventario\MovimientoInventarioController;
use App\Http\Controllers\Inventario\PrecioProductoVarianteController;
use App\Http\Controllers\Inventario\ProductoController;
use App\Http\Controllers\Inventario\ProductoImagenController;
use App\Http\Controllers\Inventario\ProductoVarianteController;
use App\Http\Controllers\Inventario\StockSucursalController;
use App\Http\Controllers\Organizacion\EmpresaController;
use App\Http\Controllers\Organizacion\SucursalController;
use App\Http\Controllers\Seguridad\PermisoController;
use App\Http\Controllers\Seguridad\RolController;
use App\Http\Controllers\Seguridad\RolPermisoController;
use App\Http\Controllers\Seguridad\UsuarioController;
use App\Http\Controllers\Transferencias\TransferenciaInventarioController;
use App\Http\Controllers\Ventas\ClienteController;
use App\Http\Controllers\Ventas\VentaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Reportes\ReporteInventarioController;
use App\Http\Controllers\Reportes\ReporteComprasController;
use App\Http\Controllers\Reportes\ReporteTransferenciasController;
use App\Http\Controllers\Reportes\ReporteVentasController;
use App\Http\Controllers\Seguridad\AuthController;
use App\Http\Controllers\Publico\CatalogoPublicoController;
use App\Http\Controllers\Publico\EmpresaPublicaController;
use App\Http\Controllers\Publico\SucursalPublicaController;
use App\Http\Controllers\Publico\ContactoPublicoController;
use App\Http\Controllers\Publico\SitemapPublicoController;
use App\Http\Controllers\Inventario\ProductoDocumentoController;


/*
|--------------------------------------------------------------------------
| ERP IRRITERRA API
|--------------------------------------------------------------------------



/*
|--------------------------------------------------------------------------
| Autenticación
|--------------------------------------------------------------------------
*/

Route::prefix('auth')
    ->group(
        function (): void {

            /*
             * Ruta pública.
             */
            Route::post(
                'login',
                [
                    AuthController::class,
                    'login'
                ]
            );
            /*
             * Rutas que requieren token.
             */
            Route::middleware(
                'auth:sanctum'
            )
                ->group(
                    function (): void {

                        Route::get(
                            'me',
                            [
                                AuthController::class,
                                'me'
                            ]
                        );

                        Route::post(
                            'logout',
                            [
                                AuthController::class,
                                'logout'
                            ]
                        );

                        Route::patch(
                            'cambiar-contrasena',
                            [
                                AuthController::class,
                                'cambiarContrasena'
                            ]
                        );

                        Route::patch(
                            'perfil',
                            [
                                AuthController::class,
                                'actualizarPerfil'
                            ]
                        );


                        Route::post(
                            'perfil/foto',
                            [
                                AuthController::class,
                                'actualizarFotoPerfil'
                            ]
                        );

                    }
                );

        }
    );

/*
|
| Módulo Organización
|
*/

/*
|--------------------------------------------------------------------------
| ERP protegido
|--------------------------------------------------------------------------
*/

Route::middleware(
    'auth:sanctum'
)
    ->group(
        function (): void {

            /*
             * Organizacion
             */

            Route::apiResource(
                'empresa',
                EmpresaController::class
            )
                ->middlewareFor(
                    ['index', 'show'],
                    'permiso:empresa.ver'
                )
                ->middlewareFor(
                    'store',
                    'permiso:empresa.crear'
                )
                ->middlewareFor(
                    'update',
                    'permiso:empresa.editar'
                )
                ->middlewareFor(
                    'destroy',
                    'permiso:empresa.eliminar'
                );


            Route::apiResource(
                'sucursal',
                SucursalController::class
            )
                ->middlewareFor(
                    ['index', 'show'],
                    'permiso:sucursal.ver'
                )
                ->middlewareFor(
                    'store',
                    'permiso:sucursal.crear'
                )
                ->middlewareFor(
                    'update',
                    'permiso:sucursal.editar'
                )
                ->middlewareFor(
                    'destroy',
                    'permiso:sucursal.eliminar'
                );


            /*
             * Seguridad
             */
            Route::apiResource(
                'rol',
                RolController::class
            )
                ->middlewareFor(
                    ['index', 'show'],
                    'permiso:rol.ver'
                )
                ->middlewareFor(
                    'store',
                    'permiso:rol.crear'
                )
                ->middlewareFor(
                    'update',
                    'permiso:rol.editar'
                )
                ->middlewareFor(
                    'destroy',
                    'permiso:rol.eliminar'
                );


            Route::apiResource(
                'permiso',
                PermisoController::class
            )
                ->middlewareFor(
                    ['index', 'show'],
                    'permiso:permiso.ver'
                )
                ->middlewareFor(
                    'store',
                    'permiso:permiso.crear'
                )
                ->middlewareFor(
                    'update',
                    'permiso:permiso.editar'
                )
                ->middlewareFor(
                    'destroy',
                    'permiso:permiso.eliminar'
                );


            Route::apiResource(
                'rol-permiso',
                RolPermisoController::class
            )
                ->middlewareFor(
                    ['index', 'show'],
                    'permiso:rol_permiso.ver'
                )
                ->middlewareFor(
                    'store',
                    'permiso:rol_permiso.crear'
                )
                ->middlewareFor(
                    'update',
                    'permiso:rol_permiso.editar'
                )
                ->middlewareFor(
                    'destroy',
                    'permiso:rol_permiso.eliminar'
                );

            Route::patch(
                'usuario/{usuario}/reactivar',
                [
                    UsuarioController::class,
                    'reactivate'
                ]
            )
                ->middleware(
                    'permiso:usuario.editar'
                );

            /*
|--------------------------------------------------------------------------
| Restablecer contraseña de usuario
|--------------------------------------------------------------------------
*/

            Route::patch(
                'usuario/{usuario}/restablecer-password',
                [
                    UsuarioController::class,
                    'restablecerPassword'
                ]
            )
                ->middleware([
                    'permiso:usuario.crear',
                    'permiso:usuario.editar'
                ]);

            Route::apiResource(
                'usuario',
                UsuarioController::class
            )
                ->middlewareFor(
                    ['index', 'show'],
                    'permiso:usuario.ver'
                )
                ->middlewareFor(
                    'store',
                    'permiso:usuario.crear'
                )
                ->middlewareFor(
                    'update',
                    'permiso:usuario.editar'
                )
                ->middlewareFor(
                    'destroy',
                    'permiso:usuario.eliminar'
                );


            /*
|--------------------------------------------------------------------------
| Reactivar categoría
|--------------------------------------------------------------------------
*/

            Route::patch(
                'categoria/{categoria}/reactivar',
                [
                    CategoriaController::class,
                    'reactivate'
                ]
            )
                ->middleware(
                    'permiso:categoria.editar'
                );
            /*
             *
             * Inventario
             */


            Route::apiResource(
                'categoria',
                CategoriaController::class
            )
                ->parameters([

                    'categoria' =>
                        'categoria'

                ])
                ->middlewareFor(
                    ['index', 'show'],
                    'permiso:categoria.ver'
                )
                ->middlewareFor(
                    'store',
                    'permiso:categoria.crear'
                )
                ->middlewareFor(
                    'update',
                    'permiso:categoria.editar'
                )
                ->middlewareFor(
                    'destroy',
                    'permiso:categoria.eliminar'
                );

            /*
|--------------------------------------------------------------------------
| Reactivar marca
|--------------------------------------------------------------------------
*/

            Route::patch(
                'marca/{marca}/reactivar',
                [
                    MarcaController::class,
                    'reactivate'
                ]
            )
                ->middleware(
                    'permiso:marca.editar'
                );


            /*
            |--------------------------------------------------------------------------
            | Marca
            |--------------------------------------------------------------------------
            */

            Route::apiResource(
                'marca',
                MarcaController::class
            )
                ->parameters([

                    'marca' =>
                        'marca'

                ])
                ->middlewareFor(
                    ['index', 'show'],
                    'permiso:marca.ver'
                )
                ->middlewareFor(
                    'store',
                    'permiso:marca.crear'
                )
                ->middlewareFor(
                    'update',
                    'permiso:marca.editar'
                )
                ->middlewareFor(
                    'destroy',
                    'permiso:marca.eliminar'
                );

            /*
|--------------------------------------------------------------------------
| Reactivar producto
|--------------------------------------------------------------------------
*/

            Route::patch(
                'producto/{producto}/reactivar',
                [
                    ProductoController::class,
                    'reactivate'
                ]
            )
                ->middleware(
                    'permiso:producto.editar'
                );

            Route::apiResource(
                'producto',
                ProductoController::class
            )
                ->parameters([

                    'producto' =>
                        'producto'

                ])
                ->middlewareFor(
                    ['index', 'show'],
                    'permiso:producto.ver'
                )
                ->middlewareFor(
                    'store',
                    'permiso:producto.crear'
                )
                ->middlewareFor(
                    'update',
                    'permiso:producto.editar'
                )
                ->middlewareFor(
                    'destroy',
                    'permiso:producto.eliminar'
                );


            Route::apiResource(
                'producto-variante',
                ProductoVarianteController::class
            )
                ->parameters([

                    'producto-variante' =>
                        'productoVariante'

                ])
                ->middlewareFor(
                    ['index', 'show'],
                    'permiso:producto_variante.ver'
                )
                ->middlewareFor(
                    'store',
                    'permiso:producto_variante.crear'
                )
                ->middlewareFor(
                    'update',
                    'permiso:producto_variante.editar'
                )
                ->middlewareFor(
                    'destroy',
                    'permiso:producto_variante.eliminar'
                );


            Route::apiResource(
                'producto-imagen',
                ProductoImagenController::class
            )
                ->parameters([

                    'producto-imagen' =>
                        'productoImagen'

                ])
                ->middlewareFor(
                    ['index', 'show'],
                    'permiso:producto_imagen.ver'
                )
                ->middlewareFor(
                    'store',
                    'permiso:producto_imagen.crear'
                )
                ->middlewareFor(
                    'update',
                    'permiso:producto_imagen.editar'
                )
                ->middlewareFor(
                    'destroy',
                    'permiso:producto_imagen.eliminar'
                );

            /*
|--------------------------------------------------------------------------
| Reactivar documento de producto
|--------------------------------------------------------------------------
*/

            Route::patch(
                'producto-documento/{productoDocumento}/reactivar',
                [
                    ProductoDocumentoController::class,
                    'reactivate'
                ]
            )
                ->middleware(
                    'permiso:producto_documento.editar'
                );


            /*
            |--------------------------------------------------------------------------
            | Documento de producto
            |--------------------------------------------------------------------------
            */

            Route::apiResource(
                'producto-documento',
                ProductoDocumentoController::class
            )
                ->parameters([

                    'producto-documento' =>
                        'productoDocumento'

                ])
                ->middlewareFor(
                    ['index', 'show'],
                    'permiso:producto_documento.ver'
                )
                ->middlewareFor(
                    'store',
                    'permiso:producto_documento.crear'
                )
                ->middlewareFor(
                    'update',
                    'permiso:producto_documento.editar'
                )
                ->middlewareFor(
                    'destroy',
                    'permiso:producto_documento.eliminar'
                );


            Route::apiResource(
                'stock-sucursal',
                StockSucursalController::class
            )
                ->parameters([

                    'stock-sucursal' =>
                        'stockSucursal'

                ])
                ->middlewareFor(
                    ['index', 'show'],
                    'permiso:stock.ver'
                )
                ->middlewareFor(
                    'store',
                    'permiso:stock.crear'
                )
                ->middlewareFor(
                    'update',
                    'permiso:stock.editar'
                )
                ->middlewareFor(
                    'destroy',
                    'permiso:stock.eliminar'
                );


            Route::apiResource(
                'movimiento-inventario',
                MovimientoInventarioController::class
            )
                ->only([

                    'index',

                    'store',

                    'show',

                    'update'

                ])
                ->parameters([

                    'movimiento-inventario' =>
                        'movimientoInventario'

                ])
                ->middlewareFor(
                    ['index', 'show'],
                    'permiso:movimiento.ver'
                )
                ->middlewareFor(
                    'store',
                    'permiso:movimiento.crear'
                )
                ->middlewareFor(
                    'update',
                    'permiso:movimiento.editar'
                );


            Route::apiResource(
                'precio-producto-variante',
                PrecioProductoVarianteController::class
            )
                ->parameters([

                    'precio-producto-variante' =>
                        'precioProductoVariante'

                ])
                ->middlewareFor(
                    ['index', 'show'],
                    'permiso:precio.ver'
                )
                ->middlewareFor(
                    'store',
                    'permiso:precio.crear'
                )
                ->middlewareFor(
                    'update',
                    'permiso:precio.editar'
                )
                ->middlewareFor(
                    'destroy',
                    'permiso:precio.eliminar'
                );


            /*
             * Transferencias
             */

            Route::patch(
                'transferencia-inventario/{transferenciaInventario}/enviar',
                [
                    TransferenciaInventarioController::class,
                    'enviar'
                ]
            )
                ->middleware(
                    'permiso:transferencia.enviar'
                );


            Route::patch(
                'transferencia-inventario/{transferenciaInventario}/completar',
                [
                    TransferenciaInventarioController::class,
                    'completar'
                ]
            )
                ->middleware(
                    'permiso:transferencia.completar'
                );


            Route::patch(
                'transferencia-inventario/{transferenciaInventario}/rechazar',
                [
                    TransferenciaInventarioController::class,
                    'rechazar'
                ]
            )
                ->middleware(
                    'permiso:transferencia.rechazar'
                );


            Route::apiResource(
                'transferencia-inventario',
                TransferenciaInventarioController::class
            )
                ->parameters([

                    'transferencia-inventario' =>
                        'transferenciaInventario'

                ])
                ->middlewareFor(
                    ['index', 'show'],
                    'permiso:transferencia.ver'
                )
                ->middlewareFor(
                    'store',
                    'permiso:transferencia.crear'
                )
                ->middlewareFor(
                    'update',
                    'permiso:transferencia.editar'
                )
                ->middlewareFor(
                    'destroy',
                    'permiso:transferencia.eliminar'
                );

            /*
            |------------------------------------------------------------
            | Reactivar cliente
            |-------------------------------------------------------------
            */

            Route::patch(
                'cliente/{cliente}/reactivar',
                [
                    ClienteController::class,
                    'reactivate'
                ]
            )
                ->middleware(
                    'permiso:cliente.editar'
                );

            /*
             * Ventas
             */
            Route::apiResource(
                'cliente',
                ClienteController::class
            )
                ->parameters([

                    'cliente' =>
                        'cliente'

                ])
                ->middlewareFor(
                    ['index', 'show'],
                    'permiso:cliente.ver'
                )
                ->middlewareFor(
                    'store',
                    'permiso:cliente.crear'
                )
                ->middlewareFor(
                    'update',
                    'permiso:cliente.editar'
                )
                ->middlewareFor(
                    'destroy',
                    'permiso:cliente.eliminar'
                );


            Route::patch(
                'venta/{venta}/completar',
                [
                    VentaController::class,
                    'completar'
                ]
            )
                ->middleware(
                    'permiso:venta.completar'
                );


            Route::patch(
                'venta/{venta}/anular',
                [
                    VentaController::class,
                    'anular'
                ]
            )
                ->middleware(
                    'permiso:venta.anular'
                );


            Route::apiResource(
                'venta',
                VentaController::class
            )
                ->parameters([

                    'venta' =>
                        'venta'

                ])
                ->middlewareFor(
                    ['index', 'show'],
                    'permiso:venta.ver'
                )
                ->middlewareFor(
                    'store',
                    'permiso:venta.crear'
                )
                ->middlewareFor(
                    'update',
                    'permiso:venta.editar'
                )
                ->middlewareFor(
                    'destroy',
                    'permiso:venta.eliminar'
                );

            /*
             * Compras
             */

            /*
            |----------------------------------------------------------
            | Reactivar proveedor
            |----------------------------------------------------------
            */

            Route::patch(
                'proveedor/{proveedor}/reactivar',
                [
                    ProveedorController::class,
                    'reactivate'
                ]
            )
                ->middleware(
                    'permiso:proveedor.editar'
                );


            Route::apiResource(
                'proveedor',
                ProveedorController::class
            )
                ->parameters([

                    'proveedor' =>
                        'proveedor'

                ])
                ->middlewareFor(
                    ['index', 'show'],
                    'permiso:proveedor.ver'
                )
                ->middlewareFor(
                    'store',
                    'permiso:proveedor.crear'
                )
                ->middlewareFor(
                    'update',
                    'permiso:proveedor.editar'
                )
                ->middlewareFor(
                    'destroy',
                    'permiso:proveedor.eliminar'
                );


            Route::patch(
                'compra/{compra}/confirmar',
                [
                    CompraController::class,
                    'confirmar'
                ]
            )
                ->middleware(
                    'permiso:compra.confirmar'
                );


            Route::patch(
                'compra/{compra}/recibir',
                [
                    CompraController::class,
                    'recibir'
                ]
            )
                ->middleware(
                    'permiso:compra.recibir'
                );


            Route::patch(
                'compra/{compra}/anular',
                [
                    CompraController::class,
                    'anular'
                ]
            )
                ->middleware(
                    'permiso:compra.anular'
                );


            Route::apiResource(
                'compra',
                CompraController::class
            )
                ->parameters([

                    'compra' =>
                        'compra'

                ])
                ->middlewareFor(
                    ['index', 'show'],
                    'permiso:compra.ver'
                )
                ->middlewareFor(
                    'store',
                    'permiso:compra.crear'
                )
                ->middlewareFor(
                    'update',
                    'permiso:compra.editar'
                )
                ->middlewareFor(
                    'destroy',
                    'permiso:compra.eliminar'
                );

            /*
             * Reportes
             */

            Route::prefix(
                'reportes/inventario'
            )
                ->group(
                    function (): void {

                        Route::get(
                            'stock',
                            [
                                ReporteInventarioController::class,
                                'stock'
                            ]
                        )
                            ->middleware(
                                'permiso:reporte_inventario.ver'
                            );


                        Route::get(
                            'stock-bajo-minimo',
                            [
                                ReporteInventarioController::class,
                                'stockBajoMinimo'
                            ]
                        )
                            ->middleware(
                                'permiso:reporte_inventario.ver'
                            );


                        Route::get(
                            'valoracion',
                            [
                                ReporteInventarioController::class,
                                'valoracion'
                            ]
                        )
                            ->middleware([

                                'permiso:reporte_inventario.ver',

                                'permiso:precio.costo_compra.ver'

                            ]);


                        Route::get(
                            'kardex',
                            [
                                ReporteInventarioController::class,
                                'kardex'
                            ]
                        )
                            ->middleware(
                                'permiso:reporte_inventario.ver'
                            );

                    }
                );

            /*
             * Reportes-Almacenero
             */

            Route::prefix('reportes')
                ->group(
                    function (): void {

                        Route::prefix('ventas')
                            ->middleware(
                                'permiso:reporte_ventas.ver'
                            )
                            ->group(
                                function (): void {

                                    Route::get(
                                        'resumen',
                                        [
                                            ReporteVentasController::class,
                                            'resumen'
                                        ]
                                    );

                                    Route::get(
                                        'productos',
                                        [
                                            ReporteVentasController::class,
                                            'productos'
                                        ]
                                    );

                                }
                            );


                        Route::prefix('compras')
                            ->middleware(
                                'permiso:reporte_compras.ver'
                            )
                            ->group(
                                function (): void {

                                    Route::get(
                                        'resumen',
                                        [
                                            ReporteComprasController::class,
                                            'resumen'
                                        ]
                                    );

                                    Route::get(
                                        'productos',
                                        [
                                            ReporteComprasController::class,
                                            'productos'
                                        ]
                                    );

                                }
                            );


                        Route::prefix('transferencias')
                            ->middleware(
                                'permiso:reporte_transferencias.ver'
                            )
                            ->group(
                                function (): void {

                                    Route::get(
                                        'resumen',
                                        [
                                            ReporteTransferenciasController::class,
                                            'resumen'
                                        ]
                                    );

                                    Route::get(
                                        'productos',
                                        [
                                            ReporteTransferenciasController::class,
                                            'productos'
                                        ]
                                    );

                                }
                            );

                    }
                );
        }
    );


/*
|--------------------------------------------------------------------------
| Catálogo público
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| Catálogo público
|--------------------------------------------------------------------------
*/

Route::prefix(
    'public'
)
    ->group(
        function (): void {

            /*
             * Catálogo general.
             */

            Route::get(
                'catalogo',
                [
                    CatalogoPublicoController::class,
                    'index'
                ]
            );


            /*
             * Detalle de producto.
             */

            Route::get(
                'productos/{id}',
                [
                    CatalogoPublicoController::class,
                    'show'
                ]
            )
                ->whereNumber(
                    'id'
                );

            /*
|--------------------------------------------------------------------------
| Empresa pública
|--------------------------------------------------------------------------
*/

            Route::get(
                'empresa',
                [
                    EmpresaPublicaController::class,
                    'show'
                ]
            );

            Route::get(
                'sucursales',
                [
                    SucursalPublicaController::class,
                    'index'
                ]
            );


            /*
|--------------------------------------------------------------------------
| Contacto público
|--------------------------------------------------------------------------
*/

            Route::post(
                'contacto',
                [
                    ContactoPublicoController::class,
                    'store'
                ]
            )
                ->middleware(
                    'throttle:5,1'
                );

            /*
|--------------------------------------------------------------------------
| Sitemap
|--------------------------------------------------------------------------
*/

            Route::get(
                'sitemap.xml',
                [
                    SitemapPublicoController::class,
                    'index'
                ]
            );
        }


    );
