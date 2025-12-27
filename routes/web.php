<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MascotaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\AdopcionController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RescateController;
use App\Http\Controllers\CheckoutController;

// Rutas públicas
Route::get('/clear-cache-force', function() {
    try {
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        return "Caché, Rutas y Configuración limpiadas correctamente.";
    } catch (\Exception $e) {
        return "Error al limpiar caché: " . $e->getMessage();
    }
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/mascotas', [MascotaController::class, 'index'])->name('mascotas.index');
Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
Route::get('/rescate', [RescateController::class, 'index'])->name('rescate.index');
Route::get('/adopcion', [AdopcionController::class, 'index'])->name('adopcion.index');

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas públicas del carrito
Route::post('/carrito', [CarritoController::class, 'store'])->name('carrito.store');
Route::get('/carrito/count', [CarritoController::class, 'getCartCount'])->name('carrito.count');

// Rutas protegidas
Route::middleware('auth')->group(function () {
    Route::get('/adopcion/{id}', [AdopcionController::class, 'show'])->name('adopcion.show');
    Route::post('/adopcion', [AdopcionController::class, 'store'])->name('adopcion.store');
    Route::get('/adopcion/exito/{nombre}', [AdopcionController::class, 'success'])->name('adopcion.success');
    
    Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
    Route::put('/carrito/{id}', [CarritoController::class, 'update'])->name('carrito.update');
    Route::delete('/carrito/{id}', [CarritoController::class, 'destroy'])->name('carrito.destroy');
    Route::get('/carrito/get', [CarritoController::class, 'getCart'])->name('carrito.get');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/exito/{id}', [CheckoutController::class, 'success'])->name('checkout.success');
    
    Route::get('/perfil', function () {
        return view('perfil');
    })->name('perfil');
});

// Rutas de administración
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\AdminController::class, 'index'])->name('index');
    
    // Auditoría
    Route::get('/auditoria', [App\Http\Controllers\AdminController::class, 'auditoria'])->name('auditoria');
    
    // Mascotas
    Route::get('/mascotas', [App\Http\Controllers\AdminController::class, 'mascotas'])->name('mascotas');
    Route::get('/mascotas/{id}/data', [App\Http\Controllers\AdminController::class, 'mascotaData'])->name('mascotas.data');
    Route::post('/mascotas', [App\Http\Controllers\AdminController::class, 'crearMascota'])->name('mascotas.store');
    Route::put('/mascotas/{id}', [App\Http\Controllers\AdminController::class, 'actualizarMascota'])->name('mascotas.update');
    Route::delete('/mascotas/{id}', [App\Http\Controllers\AdminController::class, 'eliminarMascota'])->name('mascotas.destroy');
    
    // Productos
    Route::get('/productos', [App\Http\Controllers\AdminController::class, 'productos'])->name('productos');
    Route::post('/productos', [App\Http\Controllers\AdminController::class, 'crearProducto'])->name('productos.store');
    Route::put('/productos/{id}', [App\Http\Controllers\AdminController::class, 'actualizarProducto'])->name('productos.update');
    Route::delete('/productos/{id}', [App\Http\Controllers\AdminController::class, 'eliminarProducto'])->name('productos.destroy');
    
    // Adopciones
    Route::get('/adopciones', [App\Http\Controllers\AdminController::class, 'adopciones'])->name('adopciones');
    Route::post('/adopciones/{id}/estado', [App\Http\Controllers\AdminController::class, 'actualizarEstadoAdopcion'])->name('adopciones.estado');
    
    // Pedidos
    Route::get('/pedidos', [App\Http\Controllers\AdminController::class, 'pedidos'])->name('pedidos');
    Route::post('/pedidos/{id}/estado', [App\Http\Controllers\AdminController::class, 'actualizarEstadoPedido'])->name('pedidos.estado');
});