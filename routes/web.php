<?php

use App\Http\Controllers\Admin\CertificadoController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InscricaoController;
use App\Http\Controllers\Admin\SessaoController;
use App\Http\Controllers\Admin\TreinamentoController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CertificadoPublicoController;
use App\Http\Controllers\Gestao\AreaController;
use App\Http\Controllers\Gestao\DashboardController as GestaoDashboardController;
use App\Http\Controllers\Gestao\UsuarioController;
use App\Http\Controllers\PresencaController;
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

// Check-in público de presença (link por sessão)
Route::get('/presenca/{sessao:codigo}', [PresencaController::class, 'form'])->name('presenca.form');
Route::post('/presenca/{sessao:codigo}', [PresencaController::class, 'registrar'])->name('presenca.registrar');

// Certificados (público)
Route::get('/validar', [CertificadoPublicoController::class, 'validar'])->name('certificados.validar');
Route::get('/certificado/{certificado:codigo}', [CertificadoPublicoController::class, 'mostrar'])->name('certificados.mostrar');

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
Route::middleware(['auth', 'role:gestor'])
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

        // Sessões e presença
        Route::get('presenca', [SessaoController::class, 'painel'])->name('presenca.painel');
        Route::get('treinamentos/{treinamento}/sessoes', [SessaoController::class, 'index'])->name('sessoes.index');
        Route::post('treinamentos/{treinamento}/sessoes', [SessaoController::class, 'store'])->name('sessoes.store');
        Route::delete('sessoes/{sessao}', [SessaoController::class, 'destroy'])->name('sessoes.destroy');
        Route::patch('sessoes/{sessao}/chamada', [SessaoController::class, 'toggleChamada'])->name('sessoes.chamada');
        Route::get('sessoes/{sessao}/presenca/exportar', [SessaoController::class, 'exportarPresenca'])->name('sessoes.presenca.exportar');
        Route::get('sessoes/{sessao}/presenca', [SessaoController::class, 'presenca'])->name('sessoes.presenca');
        Route::post('sessoes/{sessao}/presenca/{inscricao}', [SessaoController::class, 'togglePresenca'])->name('sessoes.presenca.toggle');

        // Certificados
        Route::get('certificados', [CertificadoController::class, 'painel'])->name('certificados.painel');
        Route::get('treinamentos/{treinamento}/certificados', [CertificadoController::class, 'index'])->name('certificados.index');
        Route::post('treinamentos/{treinamento}/certificados/emitir', [CertificadoController::class, 'emitirTodos'])->name('certificados.emitir-todos');
        Route::post('inscricoes/{inscricao}/certificado', [CertificadoController::class, 'emitir'])->name('certificados.emitir');
        Route::delete('certificados/{certificado}', [CertificadoController::class, 'destroy'])->name('certificados.destroy');
    });

/*
|--------------------------------------------------------------------------
| Administração Geral da plataforma (super-admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('gestao')
    ->name('gestao.')
    ->group(function () {
        Route::get('/', [GestaoDashboardController::class, 'index'])->name('dashboard');
        Route::resource('areas', AreaController::class)->except(['show']);
        Route::resource('usuarios', UsuarioController::class)->except(['show']);
        Route::patch('usuarios/{usuario}/senha', [UsuarioController::class, 'redefinirSenha'])->name('usuarios.senha');
        Route::patch('usuarios/{usuario}/status', [UsuarioController::class, 'alternarStatus'])->name('usuarios.status');
    });
