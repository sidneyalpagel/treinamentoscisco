<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InscricaoController;
use App\Http\Controllers\Admin\TreinamentoController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Área pública
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/agenda', [PublicController::class, 'agenda'])->name('agenda');
Route::get('/treinamentos/{treinamento:slug}', [PublicController::class, 'treinamento'])->name('treinamentos.show');
Route::post('/treinamentos/{treinamento:slug}/inscricao', [PublicController::class, 'inscrever'])->name('treinamentos.inscrever');

/*
|--------------------------------------------------------------------------
| Autenticação (administradores)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.store');
});
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Painel administrativo
|--------------------------------------------------------------------------
*/
Route::middleware('auth')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('treinamentos', TreinamentoController::class);

        // Inscrições
        Route::get('inscricoes', [InscricaoController::class, 'index'])->name('inscricoes.index');
        Route::get('inscricoes/exportar', [InscricaoController::class, 'exportar'])->name('inscricoes.exportar');
        Route::patch('inscricoes/{inscricao}', [InscricaoController::class, 'update'])->name('inscricoes.update');
        Route::delete('inscricoes/{inscricao}', [InscricaoController::class, 'destroy'])->name('inscricoes.destroy');
    });
