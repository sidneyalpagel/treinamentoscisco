<?php

namespace App\Http\Controllers;

use App\Http\Requests\InscricaoRequest;
use App\Models\Inscricao;
use App\Models\Treinamento;
use Illuminate\Http\RedirectResponse;
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

    /**
     * Processa a inscrição pública em um treinamento.
     */
    public function inscrever(InscricaoRequest $request, Treinamento $treinamento): RedirectResponse
    {
        abort_unless($treinamento->estaPublicado(), 404);

        if (! $treinamento->inscricoesAbertas()) {
            return back()->with('erro', 'As inscrições para este treinamento não estão disponíveis.');
        }

        $inscricao = $treinamento->inscricoes()->create(array_merge(
            $request->validated(),
            ['status' => Inscricao::STATUS_CONFIRMADA],
        ));

        return redirect()
            ->route('treinamentos.show', $treinamento)
            ->with('inscricao_sucesso', $inscricao->id);
    }
}
