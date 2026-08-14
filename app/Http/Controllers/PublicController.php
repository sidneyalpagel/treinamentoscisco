<?php

namespace App\Http\Controllers;

use App\Http\Requests\InscricaoRequest;
use App\Mail\LinkReuniao;
use App\Models\Inscricao;
use App\Models\Reuniao;
use App\Models\Treinamento;
use App\Support\JitsiToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
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

        // Treinamento online com sala: envia o link da reunião por e-mail ao inscrito.
        if ($treinamento->modalidadeOnline() && $treinamento->temSala()) {
            try {
                Mail::to($inscricao->email)->send(new LinkReuniao(
                    $treinamento,
                    route('sala.publica', $treinamento->sala_codigo),
                    $inscricao->nome,
                ));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()
            ->route('treinamentos.show', $treinamento)
            ->with('inscricao_sucesso', $inscricao->id);
    }

    /**
     * Entrada pública na sala (link único do participante).
     * Gera um token de participante na hora e redireciona ao Jitsi.
     */
    public function entrarSala(string $codigo): RedirectResponse
    {
        $treinamento = Treinamento::publicados()->where('sala_codigo', $codigo)->first();

        abort_unless($treinamento, 404);
        abort_unless(JitsiToken::configurado(), 503);

        $url = JitsiToken::url($treinamento->sala_codigo, [], moderador: false);

        return redirect()->away($url);
    }

    /**
     * Entrada pública na sala de uma reunião avulsa (link único do participante).
     */
    public function entrarReuniao(string $codigo): RedirectResponse
    {
        $reuniao = Reuniao::where('sala_codigo', $codigo)->first();

        abort_unless($reuniao, 404);
        abort_unless(JitsiToken::configurado(), 503);

        $url = JitsiToken::url($reuniao->sala_codigo, [], moderador: false);

        return redirect()->away($url);
    }
}
