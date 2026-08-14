<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TreinamentoRequest;
use App\Models\Treinamento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TreinamentoController extends Controller
{
    /**
     * Lista de treinamentos com busca e filtro por status.
     */
    public function index(Request $request): View
    {
        $treinamentos = Treinamento::query()
            ->doUsuario($request->user()->id)
            ->when($request->filled('busca'), function ($q) use ($request) {
                $termo = $request->string('busca');
                $q->where(fn ($sub) => $sub
                    ->where('titulo', 'like', "%{$termo}%")
                    ->orWhere('instrutor', 'like', "%{$termo}%"));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->orderByDesc('data_inicio')
            ->paginate(10)
            ->withQueryString();

        return view('admin.treinamentos.index', [
            'treinamentos' => $treinamentos,
            'statusDisponiveis' => Treinamento::statusDisponiveis(),
            'filtros' => $request->only(['busca', 'status']),
        ]);
    }

    public function create(): View
    {
        return view('admin.treinamentos.create', [
            'treinamento' => new Treinamento(['status' => Treinamento::STATUS_RASCUNHO]),
            'statusDisponiveis' => Treinamento::statusDisponiveis(),
            'modalidadesDisponiveis' => Treinamento::modalidadesDisponiveis(),
        ]);
    }

    public function store(TreinamentoRequest $request): RedirectResponse
    {
        $treinamento = Treinamento::create($request->validated() + [
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.treinamentos.show', $treinamento)
            ->with('sucesso', 'Treinamento cadastrado com sucesso.');
    }

    public function show(Treinamento $treinamento): View
    {
        $this->autorizarDono($treinamento);

        return view('admin.treinamentos.show', compact('treinamento'));
    }

    public function edit(Treinamento $treinamento): View
    {
        $this->autorizarDono($treinamento);

        return view('admin.treinamentos.edit', [
            'treinamento' => $treinamento,
            'statusDisponiveis' => Treinamento::statusDisponiveis(),
            'modalidadesDisponiveis' => Treinamento::modalidadesDisponiveis(),
        ]);
    }

    public function update(TreinamentoRequest $request, Treinamento $treinamento): RedirectResponse
    {
        $this->autorizarDono($treinamento);

        $treinamento->update($request->validated());

        return redirect()
            ->route('admin.treinamentos.show', $treinamento)
            ->with('sucesso', 'Treinamento atualizado com sucesso.');
    }

    public function destroy(Treinamento $treinamento): RedirectResponse
    {
        $this->autorizarDono($treinamento);

        $treinamento->delete();

        return redirect()
            ->route('admin.treinamentos.index')
            ->with('sucesso', 'Treinamento removido com sucesso.');
    }

    /**
     * Garante que o treinamento pertence ao gestor autenticado.
     */
    private function autorizarDono(Treinamento $treinamento): void
    {
        abort_unless($treinamento->user_id === auth()->id(), 403);
    }
}
