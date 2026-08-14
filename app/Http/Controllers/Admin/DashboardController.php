<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inscricao;
use App\Models\Treinamento;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        $metricas = [
            'total' => Treinamento::doUsuario($userId)->count(),
            'publicados' => Treinamento::doUsuario($userId)->where('status', Treinamento::STATUS_PUBLICADO)->count(),
            'inscricoes' => Inscricao::whereHas('treinamento', fn ($q) => $q->doUsuario($userId))->count(),
            'proximos' => Treinamento::doUsuario($userId)->proximos()->count(),
        ];

        $proximosTreinamentos = Treinamento::doUsuario($userId)->proximos()
            ->limit(5)
            ->get();

        $recentes = Treinamento::doUsuario($userId)->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('metricas', 'proximosTreinamentos', 'recentes'));
    }
}
