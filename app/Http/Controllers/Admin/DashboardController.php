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
        $metricas = [
            'total' => Treinamento::count(),
            'publicados' => Treinamento::where('status', Treinamento::STATUS_PUBLICADO)->count(),
            'inscricoes' => Inscricao::count(),
            'proximos' => Treinamento::proximos()->count(),
        ];

        $proximosTreinamentos = Treinamento::proximos()
            ->limit(5)
            ->get();

        $recentes = Treinamento::latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('metricas', 'proximosTreinamentos', 'recentes'));
    }
}
