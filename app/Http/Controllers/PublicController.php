<?php

namespace App\Http\Controllers;

use App\Models\Treinamento;
use Illuminate\View\View;

class PublicController extends Controller
{
    /**
     * Página inicial: destaque + próximos treinamentos publicados.
     */
    public function home(): View
    {
        $proximos = Treinamento::publicados()
            ->proximos()
            ->limit(6)
            ->get();

        return view('public.home', compact('proximos'));
    }

    /**
     * Agenda geral de treinamentos publicados.
     */
    public function agenda(): View
    {
        $treinamentos = Treinamento::publicados()
            ->orderBy('data_inicio')
            ->get()
            ->groupBy(fn (Treinamento $t) => $t->data_inicio->translatedFormat('F \d\e Y'));

        return view('public.agenda', compact('treinamentos'));
    }

    /**
     * Página pública de um treinamento (lista de inscrição).
     */
    public function treinamento(Treinamento $treinamento): View
    {
        abort_unless($treinamento->estaPublicado(), 404);

        return view('public.treinamento', compact('treinamento'));
    }
}
