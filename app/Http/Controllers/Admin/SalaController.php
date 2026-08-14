<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Treinamento;
use App\Support\JitsiToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class SalaController extends Controller
{
    /** Cria a sala de videoconferência do treinamento. */
    public function criar(Treinamento $treinamento): RedirectResponse
    {
        $this->autorizar($treinamento);

        if (! $treinamento->modalidadeOnline()) {
            return back()->with('erro', 'Salas de videoconferência são apenas para treinamentos Online ou Híbridos.');
        }

        if (! JitsiToken::configurado()) {
            return back()->with('erro', 'Configure a Videoconferência (Jitsi) em Configurações antes de criar salas.');
        }

        if (! $treinamento->temSala()) {
            $treinamento->update([
                'sala_codigo' => $this->gerarCodigo($treinamento),
                'sala_criada_em' => now(),
            ]);
        }

        return back()->with('sucesso', 'Sala de videoconferência criada.');
    }

    /** Entra na sala como moderador (host). */
    public function entrar(Treinamento $treinamento): RedirectResponse
    {
        $this->autorizar($treinamento);
        abort_unless($treinamento->temSala(), 404);

        $url = JitsiToken::url($treinamento->sala_codigo, [
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
        ], moderador: true);

        return redirect()->away($url);
    }

    /** Remove a sala (desativa a videoconferência do treinamento). */
    public function remover(Treinamento $treinamento): RedirectResponse
    {
        $this->autorizar($treinamento);

        $treinamento->update(['sala_codigo' => null, 'sala_criada_em' => null]);

        return back()->with('sucesso', 'Sala removida.');
    }

    private function gerarCodigo(Treinamento $treinamento): string
    {
        $base = Str::slug(Str::limit($treinamento->titulo, 30, '')) ?: 'sala';

        do {
            $codigo = $base.'-'.Str::lower(Str::random(6));
        } while (Treinamento::where('sala_codigo', $codigo)->exists());

        return $codigo;
    }

    private function autorizar(Treinamento $treinamento): void
    {
        abort_unless($treinamento->user_id === auth()->id(), 403);
    }
}
