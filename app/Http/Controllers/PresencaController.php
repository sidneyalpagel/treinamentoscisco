<?php

namespace App\Http\Controllers;

use App\Models\Presenca;
use App\Models\Sessao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PresencaController extends Controller
{
    /**
     * Formulário público de registro de presença (link por sessão).
     */
    public function form(Sessao $sessao): View
    {
        $sessao->load('treinamento');

        return view('public.presenca', compact('sessao'));
    }

    /**
     * Registra a presença do participante via e-mail da inscrição.
     */
    public function registrar(Request $request, Sessao $sessao): RedirectResponse
    {
        $sessao->load('treinamento');

        if (! $sessao->presenca_aberta) {
            return back()->with('erro', 'O registro de presença desta sessão está fechado no momento.');
        }

        $dados = $request->validate(
            ['email' => ['required', 'email']],
            [],
            ['email' => 'e-mail']
        );

        $inscricao = $sessao->treinamento
            ->inscricoes()
            ->confirmadas()
            ->where('email', $dados['email'])
            ->first();

        if (! $inscricao) {
            return back()->withInput()->with('erro', 'Não encontramos uma inscrição confirmada com este e-mail para este treinamento.');
        }

        $presenca = $sessao->presencas()->firstOrNew(['inscricao_id' => $inscricao->id]);
        $jaRegistrada = $presenca->exists;

        if (! $jaRegistrada) {
            $presenca->fill([
                'registrado_em' => now(),
                'origem' => Presenca::ORIGEM_AUTO,
            ])->save();
        }

        return redirect()
            ->route('presenca.form', $sessao)
            ->with('presenca_ok', $inscricao->nome)
            ->with('presenca_repetida', $jaRegistrada);
    }
}
