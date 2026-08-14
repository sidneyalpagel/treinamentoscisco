<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AtivacaoController extends Controller
{
    /** Validade do link de convite, em dias. */
    private const VALIDADE_DIAS = 7;

    /** Tela para o usuário confirmar o cadastro e definir a senha. */
    public function form(string $token): View
    {
        $usuario = $this->usuarioPorToken($token);

        return view('auth.ativar', [
            'token' => $token,
            'usuario' => $usuario,
            'invalido' => is_null($usuario),
            'expirado' => $usuario && $this->expirado($usuario),
        ]);
    }

    /** Define a senha, ativa o cadastro e autentica o usuário. */
    public function store(Request $request, string $token): RedirectResponse
    {
        $usuario = $this->usuarioPorToken($token);

        if (is_null($usuario) || $this->expirado($usuario)) {
            return redirect()->route('ativar.form', $token);
        }

        $dados = $request->validate(
            ['password' => ['required', 'string', 'min:8', 'confirmed']],
            [],
            ['password' => 'senha']
        );

        $usuario->forceFill([
            'password' => $dados['password'],
            'convite_token' => null,
            'convite_enviado_em' => null,
            'email_verified_at' => now(),
        ])->save();

        Auth::login($usuario);
        $request->session()->regenerate();

        $rota = $usuario->isAdmin() ? route('gestao.dashboard') : route('admin.dashboard');

        return redirect()->to($rota)->with('sucesso', 'Cadastro confirmado! Bem-vindo(a) à plataforma.');
    }

    private function usuarioPorToken(string $token): ?User
    {
        // token muito curto nem consulta o banco
        if (strlen($token) < 32) {
            return null;
        }

        return User::where('convite_token', $token)->first();
    }

    private function expirado(User $usuario): bool
    {
        return $usuario->convite_enviado_em
            && $usuario->convite_enviado_em->lt(now()->subDays(self::VALIDADE_DIAS));
    }
}
