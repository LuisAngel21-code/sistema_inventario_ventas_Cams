<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

// HU-01/02/03: Auth publico - Usuario del sistema
Route::get('/login', [AuthController::class, 'mostrarLogin'])->name('login');
Route::post('/login', [AuthController::class, 'iniciarSesion']);
Route::post('/login/verificar', [AuthController::class, 'verificarCodigo'])->name('login.verificar');
Route::get('/login/cancelar', [AuthController::class, 'cancelarVerificacion'])->name('login.cancelar');
Route::get('/recuperar', [AuthController::class, 'mostrarRecuperacion'])->name('recuperar');
Route::post('/recuperar', [AuthController::class, 'enviarEnlaceRecuperacion']);

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'cerrarSesion'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:administrador,jefe')
        ->name('dashboard');

    Route::middleware('role:encargado_almacen,administrador,jefe')->group(function () {
        Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
        Route::get('/productos/crear', [ProductoController::class, 'create'])->name('productos.create');
        Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
        Route::get('/productos/{producto}/editar', [ProductoController::class, 'edit'])->name('productos.edit');
        Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
        Route::put('/productos/{producto}/stock', [ProductoController::class, 'actualizarStock'])->name('productos.stock');
        Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');

        Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
        Route::get('/inventario/movimientos', [InventarioController::class, 'movimientos'])->name('inventario.movimientos');
        Route::get('/inventario/ingreso', [InventarioController::class, 'mostrarRegistroIngreso'])->name('inventario.ingreso');
        Route::post('/inventario/ingreso', [InventarioController::class, 'registrarIngreso']);
    });

    Route::middleware('role:encargado_tienda,administrador,jefe')->group(function () {
        Route::get('/ventas/nueva', [VentaController::class, 'create'])->name('ventas.create');
        Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');
        Route::get('/ventas', [VentaController::class, 'historial'])->name('ventas.historial');
        Route::get('/ventas/{venta}', [VentaController::class, 'detalle'])->name('ventas.detalle');
    });

    Route::middleware('role:administrador,jefe')->group(function () {
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    });

    // HU-13: Sucursales - solo Administrador
    Route::middleware('role:administrador')->group(function () {
        Route::get('/sucursales', [SucursalController::class, 'index'])->name('sucursales.index');
        Route::get('/sucursales/crear', [SucursalController::class, 'create'])->name('sucursales.create');
        Route::post('/sucursales', [SucursalController::class, 'store'])->name('sucursales.store');
        Route::put('/sucursales/{sucursal}/estado', [SucursalController::class, 'cambiarEstado'])->name('sucursales.estado');
    });

    Route::get('/', function () {
        $destino = match(auth()->user()->rol) {
            'administrador', 'jefe' => route('dashboard'),
            'encargado_almacen'     => route('productos.index'),
            'encargado_tienda'      => route('ventas.historial'),
            default                 => route('dashboard'),
        };

        return redirect($destino);
    });
});