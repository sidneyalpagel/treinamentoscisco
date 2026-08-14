<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inscricao;
use App\Models\Presenca;
use App\Models\Sessao;
use App\Models\Treinamento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SessaoController extends Controller
{
    /**
     * Visão geral das sessões (todas as agendas).
     */
    public function painel(): View
    {
        $sessoes = Sessao::query()
            ->whereHas('treinamento', fn ($q) => $q->doUsuario(auth()->id()))
            ->with('treinamento')
            ->withCount('presencas')
            ->orderByDesc('data')
            ->orderByDesc('hora_inicio')
            ->paginate(20);

        return view('admin.presenca.painel', compact('sessoes'));
    }

    /**
     * Sessões de um treinamento.
     */
    public function index(Treinamento $treinamento): View
    {
        $this->autorizarTreinamento($treinamento);

        $treinamento->load(['sessoes' => fn ($q) => $q->withCount('presencas')]);
        $totalConfirmados = $treinamento->totalConfirmadas();

        return view('admin.sessoes.index', compact('treinamento', 'totalConfirmados'));
    }

    public function store(Request $request, Treinamento $treinamento): RedirectResponse
    {
        $this->autorizarTreinamento($treinamento);

        $dados = $request->validate([
            'titulo' => ['nullable', 'string', 'max:255'],
            'data' => ['required', 'date'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fim' => ['nullable', 'date_format:H:i', 'after:hora_inicio'],
        ], [], [
            'data' => 'data',
            'hora_inicio' => 'hora de início',
            'hora_fim' => 'hora de término',
        ]);

        $treinamento->sessoes()->create($dados);

        return back()->with('sucesso', 'Sessão adicionada.');
    }

    public function destroy(Sessao $sessao): RedirectResponse
    {
        $this->autorizarSessao($sessao);

        $treinamento = $sessao->treinamento;
        $sessao->delete();

        return redirect()
            ->route('admin.sessoes.index', $treinamento)
            ->with('sucesso', 'Sessão removida.');
    }

    /**
     * Abre/fecha o check-in público de presença.
     */
    public function toggleChamada(Sessao $sessao): RedirectResponse
    {
        $this->autorizarSessao($sessao);

        $sessao->update(['presenca_aberta' => ! $sessao->presenca_aberta]);

        return back()->with('sucesso', $sessao->presenca_aberta
            ? 'Check-in de presença aberto.'
            : 'Check-in de presença fechado.');
    }

    /**
     * Lista de presença de uma sessão.
     */
    public function presenca(Sessao $sessao): View
    {
        $this->autorizarSessao($sessao);

        $sessao->load('treinamento');

        $inscritos = $sessao->treinamento
            ->inscricoes()
            ->confirmadas()
            ->orderBy('nome')
            ->get();

        $presentes = $sessao->presencas()->pluck('registrado_em', 'inscricao_id');

        return view('admin.presenca.sessao', compact('sessao', 'inscritos', 'presentes'));
    }

    /**
     * Marca/desmarca a presença de um participante (admin).
     */
    public function togglePresenca(Sessao $sessao, Inscricao $inscricao): RedirectResponse
    {
        $this->autorizarSessao($sessao);
        abort_unless($inscricao->treinamento_id === $sessao->treinamento_id, 404);

        $presenca = $sessao->presencas()->where('inscricao_id', $inscricao->id)->first();

        if ($presenca) {
            $presenca->delete();
            $msg = 'Presença removida.';
        } else {
            $sessao->presencas()->create([
                'inscricao_id' => $inscricao->id,
                'registrado_em' => now(),
                'origem' => Presenca::ORIGEM_ADMIN,
            ]);
            $msg = 'Presença registrada.';
        }

        return back()->with('sucesso', $msg);
    }

    /**
     * Exporta a lista de presença de uma sessão em CSV.
     */
    public function exportarPresenca(Sessao $sessao): StreamedResponse
    {
        $this->autorizarSessao($sessao);

        $sessao->load('treinamento');
        $inscritos = $sessao->treinamento->inscricoes()->confirmadas()->orderBy('nome')->get();
        $presentes = $sessao->presencas()->pluck('registrado_em', 'inscricao_id');

        $nomeArquivo = 'presenca-'.$sessao->codigo.'-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($sessao, $inscritos, $presentes) {
            $saida = fopen('php://output', 'w');
            fwrite($saida, "\xEF\xBB\xBF");
            fputcsv($saida, ['Treinamento', $sessao->treinamento->titulo], ';');
            fputcsv($saida, ['Sessão', $sessao->nome_exibicao.' - '.$sessao->horario], ';');
            fputcsv($saida, [], ';');
            fputcsv($saida, ['Nome', 'E-mail', 'Órgão/Setor', 'Presente', 'Horário do registro'], ';');

            foreach ($inscritos as $i) {
                $presente = $presentes->has($i->id);
                fputcsv($saida, [
                    $i->nome,
                    $i->email,
                    $i->orgao,
                    $presente ? 'Sim' : 'Não',
                    $presente ? \Illuminate\Support\Carbon::parse($presentes[$i->id])->format('d/m/Y H:i') : '',
                ], ';');
            }

            fclose($saida);
        }, $nomeArquivo, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function autorizarTreinamento(Treinamento $treinamento): void
    {
        abort_unless($treinamento->user_id === auth()->id(), 403);
    }

    private function autorizarSessao(Sessao $sessao): void
    {
        abort_unless($sessao->treinamento->user_id === auth()->id(), 403);
    }
}
