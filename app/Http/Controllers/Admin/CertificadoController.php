<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificado;
use App\Models\Inscricao;
use App\Models\Treinamento;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CertificadoController extends Controller
{
    /**
     * Visão geral: treinamentos que emitem certificado.
     */
    public function painel(): View
    {
        $treinamentos = Treinamento::query()
            ->doUsuario(auth()->id())
            ->where('gera_certificado', true)
            ->withCount([
                'inscricoes as confirmados_count' => fn ($q) => $q->confirmadas(),
                'certificados as emitidos_count',
            ])
            ->orderByDesc('data_inicio')
            ->paginate(20);

        return view('admin.certificados.painel', compact('treinamentos'));
    }

    /**
     * Gestão de certificados de um treinamento.
     */
    public function index(Treinamento $treinamento): View
    {
        $this->autorizarTreinamento($treinamento);

        $inscritos = $treinamento->inscricoes()
            ->confirmadas()
            ->with('certificado')
            ->orderBy('nome')
            ->get();

        return view('admin.certificados.index', compact('treinamento', 'inscritos'));
    }

    /**
     * Emite o certificado de um participante.
     */
    public function emitir(Inscricao $inscricao): RedirectResponse
    {
        $treinamento = $inscricao->treinamento;
        $this->autorizarTreinamento($treinamento);

        if (! $treinamento->gera_certificado) {
            return back()->with('erro', 'Este treinamento não está configurado para emitir certificados.');
        }

        if ($inscricao->status !== Inscricao::STATUS_CONFIRMADA) {
            return back()->with('erro', 'Apenas participantes com inscrição confirmada podem receber certificado.');
        }

        Certificado::firstOrCreate(
            ['inscricao_id' => $inscricao->id],
            ['emitido_em' => now(), 'carga_horaria' => $treinamento->carga_horaria],
        );

        return back()->with('sucesso', 'Certificado emitido.');
    }

    /**
     * Emite certificados para todos os confirmados que ainda não têm.
     */
    public function emitirTodos(Treinamento $treinamento): RedirectResponse
    {
        $this->autorizarTreinamento($treinamento);

        if (! $treinamento->gera_certificado) {
            return back()->with('erro', 'Este treinamento não está configurado para emitir certificados.');
        }

        $pendentes = $treinamento->inscricoes()
            ->confirmadas()
            ->whereDoesntHave('certificado')
            ->get();

        foreach ($pendentes as $inscricao) {
            Certificado::create([
                'inscricao_id' => $inscricao->id,
                'emitido_em' => now(),
                'carga_horaria' => $treinamento->carga_horaria,
            ]);
        }

        return back()->with('sucesso', $pendentes->count().' certificado(s) emitido(s).');
    }

    /**
     * Revoga (remove) um certificado.
     */
    public function destroy(Certificado $certificado): RedirectResponse
    {
        abort_unless($certificado->inscricao->treinamento->user_id === auth()->id(), 403);

        $certificado->delete();

        return back()->with('sucesso', 'Certificado revogado.');
    }

    private function autorizarTreinamento(Treinamento $treinamento): void
    {
        abort_unless($treinamento->user_id === auth()->id(), 403);
    }
}
