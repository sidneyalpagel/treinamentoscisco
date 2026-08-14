<?php

namespace App\Http\Controllers\Gestao;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Treinamento;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $metricas = [
            'gestores' => User::where('role', User::ROLE_GESTOR)->count(),
            'gestores_ativos' => User::where('role', User::ROLE_GESTOR)->where('ativo', true)->count(),
            'areas' => Area::count(),
            'treinamentos' => Treinamento::count(),
        ];

        $gestoresRecentes = User::where('role', User::ROLE_GESTOR)
            ->with('area')
            ->withCount('treinamentos')
            ->latest()
            ->limit(6)
            ->get();

        return view('gestao.dashboard', compact('metricas', 'gestoresRecentes'));
    }
}
