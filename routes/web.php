<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VendedorController;
use App\Http\Controllers\AuditorController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\MetaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ActividadController;

// ── Autenticación ─────────────────────────────────────────
Route::get('/',      [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',  [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Admin ─────────────────────────────────────────────────
Route::middleware(['auth', 'solo.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Usuarios
    Route::resource('usuarios', UsuarioController::class)->except(['show']);

    // Metas
    Route::resource('metas', MetaController::class)->except(['show']);

    // Reportes
    Route::get('/reportes',         [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/pdf',     [ReporteController::class, 'pdf'])->name('reportes.pdf');
});



// ── Vendedor ──────────────────────────────────────────────
Route::middleware(['auth', 'solo.vendedor'])->prefix('vendedor')->name('vendedor.')->group(function () {
    Route::get('/dashboard', [VendedorController::class, 'dashboard'])->name('dashboard');
    Route::resource('actividades', ActividadController::class)->except(['show','destroy']);
});

// ── Auditor ───────────────────────────────────────────────
Route::middleware(['auth', 'solo.auditor'])->prefix('auditor')->name('auditor.')->group(function () {
    Route::get('/dashboard',              [AuditorController::class, 'dashboard'])->name('dashboard');
    Route::get('/reportes',               [AuditorController::class, 'reportes'])->name('reportes.index');
    Route::get('/reportes/pdf',           [AuditorController::class, 'pdf'])->name('reportes.pdf');
    Route::get('/estadisticas',           [AuditorController::class, 'estadisticas'])->name('estadisticas.index');
});