<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inscricao;
use App\Models\Treinamento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InscricaoController extends Controller
{
    /**
     * Monta a query de inscrições a partir dos filtros da requisição.
     */
    private function filtrar(Request $request)
    {
        return Inscricao::query()
            ->with('treinamento')
            ->whereHas('treinamento', fn ($q) => $q->doUsuario($request->user()->id))
            ->when($request->filled('treinamento'), fn ($q) => $q->where('treinamento_id', $request->integer('treinamento')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('busca'), function ($q) use ($request) {
                $termo = $request->string('busca');
                $q->where(fn ($sub) => $sub
                    ->where('nome', 'like', "%{$termo}%")
                    ->orWhere('email', 'like', "%{$termo}%")
                    ->orWhere('orgao', 'like', "%{$termo}%"));
            });
    }

    public function index(Request $request): View
    {
        $inscricoes = $this->filtrar($request)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.inscricoes.index', [
            'inscricoes' => $inscricoes,
            'treinamentos' => Treinamento::doUsuario($request->user()->id)->orderBy('titulo')->get(['id', 'titulo']),
            'statusDisponiveis' => Inscricao::statusDisponiveis(),
            'filtros' => $request->only(['treinamento', 'status', 'busca']),
            'total' => $inscricoes->total(),
        ]);
    }

    public function update(Request $request, Inscricao $inscricao): RedirectResponse
    {
        $this->autorizarDono($inscricao);

        $dados = Validator::make($request->all(), [
            'status' => ['required', 'in:'.implode(',', array_keys(Inscricao::statusDisponiveis()))],
        ])->validate();

        $inscricao->update($dados);

        return back()->with('sucesso', 'Status da inscrição atualizado.');
    }

    public function destroy(Inscricao $inscricao): RedirectResponse
    {
        $this->autorizarDono($inscricao);

        $inscricao->delete();

        return back()->with('sucesso', 'Inscrição removida.');
    }

    /**
     * Garante que a inscrição pertence a um treinamento do gestor autenticado.
     */
    private function autorizarDono(Inscricao $inscricao): void
    {
        abort_unless($inscricao->treinamento->user_id === auth()->id(), 403);
    }

    /**
     * Exporta as inscrições filtradas em CSV.
     */
    public function exportar(Request $request): StreamedResponse
    {
        $inscricoes = $this->filtrar($request)->latest()->get();

        $nomeArquivo = 'inscricoes-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($inscricoes) {
            $saida = fopen('php://output', 'w');
            // BOM para acentuação correta no Excel
            fwrite($saida, "\xEF\xBB\xBF");
            fputcsv($saida, ['Treinamento', 'Nome', 'E-mail', 'CPF', 'Telefone', 'Órgão/Setor', 'Cargo', 'Status', 'Data da inscrição'], ';');

            foreach ($inscricoes as $i) {
                fputcsv($saida, [
                    $i->treinamento?->titulo,
                    $i->nome,
                    $i->email,
                    $i->cpf,
                    $i->telefone,
                    $i->orgao,
                    $i->cargo,
                    $i->status_label,
                    $i->created_at->format('d/m/Y H:i'),
                ], ';');
            }

            fclose($saida);
        }, $nomeArquivo, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
