<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReuniaoRequest;
use App\Models\Reuniao;
use App\Support\JitsiToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReuniaoController extends Controller
{
    public function index(Request $request): View
    {
        $reunioes = Reuniao::doUsuario($request->user()->id)
            ->withCount('gravacoes')
            ->orderByDesc('data_inicio')
            ->paginate(10);

        return view('admin.reunioes.index', compact('reunioes'));
    }

    public function create(): View
    {
        return view('admin.reunioes.create', ['reuniao' => new Reuniao]);
    }

    public function store(ReuniaoRequest $request): RedirectResponse
    {
        $reuniao = new Reuniao($request->validated());
        $reuniao->user_id = $request->user()->id;
        $reuniao->sala_codigo = Reuniao::gerarSalaCodigo($reuniao->titulo);
        $reuniao->save();

        return redirect()->route('admin.reunioes.show', $reuniao)->with('sucesso', 'Reunião criada.');
    }

    public function show(Reuniao $reuniao): View
    {
        $this->autorizar($reuniao);

        return view('admin.reunioes.show', compact('reuniao'));
    }

    public function edit(Reuniao $reuniao): View
    {
        $this->autorizar($reuniao);

        return view('admin.reunioes.edit', compact('reuniao'));
    }

    public function update(ReuniaoRequest $request, Reuniao $reuniao): RedirectResponse
    {
        $this->autorizar($reuniao);

        $reuniao->update($request->validated());

        return redirect()->route('admin.reunioes.show', $reuniao)->with('sucesso', 'Reunião atualizada.');
    }

    public function destroy(Reuniao $reuniao): RedirectResponse
    {
        $this->autorizar($reuniao);

        $reuniao->delete();

        return redirect()->route('admin.reunioes.index')->with('sucesso', 'Reunião removida.');
    }

    /** Entra na sala como moderador (host). */
    public function entrar(Reuniao $reuniao): RedirectResponse
    {
        $this->autorizar($reuniao);
        abort_unless(JitsiToken::configurado(), 503);

        $url = JitsiToken::url($reuniao->sala_codigo, [
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
        ], moderador: true);

        return redirect()->away($url);
    }

    private function autorizar(Reuniao $reuniao): void
    {
        abort_unless($reuniao->user_id === auth()->id(), 403);
    }
}
