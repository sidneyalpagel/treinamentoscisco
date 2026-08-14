<?php

namespace App\Http\Controllers\Gestao;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gestao\UsuarioRequest;
use App\Models\Area;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function index(Request $request): View
    {
        $usuarios = User::query()
            ->with('area')
            ->withCount('treinamentos')
            ->when($request->filled('busca'), function ($q) use ($request) {
                $termo = $request->string('busca');
                $q->where(fn ($sub) => $sub->where('name', 'like', "%{$termo}%")->orWhere('email', 'like', "%{$termo}%"));
            })
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->string('role')))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('gestao.usuarios.index', [
            'usuarios' => $usuarios,
            'rolesDisponiveis' => User::rolesDisponiveis(),
            'filtros' => $request->only(['busca', 'role']),
        ]);
    }

    public function create(): View
    {
        return view('gestao.usuarios.create', [
            'usuario' => new User(['role' => User::ROLE_GESTOR, 'ativo' => true]),
            'areas' => Area::ativas()->orderBy('nome')->get(),
            'rolesDisponiveis' => User::rolesDisponiveis(),
        ]);
    }

    public function store(UsuarioRequest $request): RedirectResponse
    {
        $dados = $request->safe()->only(['name', 'email', 'role', 'area_id', 'ativo', 'password']);

        User::create($dados);

        return redirect()->route('gestao.usuarios.index')->with('sucesso', 'Usuário cadastrado com sucesso.');
    }

    public function edit(User $usuario): View
    {
        return view('gestao.usuarios.edit', [
            'usuario' => $usuario,
            'areas' => Area::ativas()->orderBy('nome')->get(),
            'rolesDisponiveis' => User::rolesDisponiveis(),
        ]);
    }

    public function update(UsuarioRequest $request, User $usuario): RedirectResponse
    {
        // Não altera a senha aqui (existe ação dedicada de redefinição)
        $usuario->update($request->safe()->only(['name', 'email', 'role', 'area_id', 'ativo']));

        return redirect()->route('gestao.usuarios.index')->with('sucesso', 'Usuário atualizado com sucesso.');
    }

    /**
     * Redefinição de senha do usuário.
     */
    public function redefinirSenha(Request $request, User $usuario): RedirectResponse
    {
        $dados = Validator::make($request->all(), [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [], ['password' => 'senha'])->validate();

        $usuario->update(['password' => $dados['password']]);

        return back()->with('sucesso', 'Senha redefinida com sucesso.');
    }

    /**
     * Ativa/desativa o acesso do usuário.
     */
    public function alternarStatus(User $usuario): RedirectResponse
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('erro', 'Você não pode desativar o próprio acesso.');
        }

        $usuario->update(['ativo' => ! $usuario->ativo]);

        return back()->with('sucesso', $usuario->ativo ? 'Acesso ativado.' : 'Acesso desativado.');
    }

    public function destroy(User $usuario): RedirectResponse
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('erro', 'Você não pode remover o próprio usuário.');
        }

        if ($usuario->treinamentos()->exists()) {
            return back()->with('erro', 'Este gestor possui treinamentos cadastrados. Desative o acesso em vez de remover, para preservar o histórico.');
        }

        $usuario->delete();

        return back()->with('sucesso', 'Usuário removido.');
    }
}
