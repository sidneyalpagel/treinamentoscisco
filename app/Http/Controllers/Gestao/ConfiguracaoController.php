<?php

namespace App\Http\Controllers\Gestao;

use App\Http\Controllers\Controller;
use App\Models\Configuracao;
use App\Support\JitsiToken;
use App\Support\SmtpRuntime;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ConfiguracaoController extends Controller
{
    /** Tela de configurações da plataforma (e-mail e videoconferência). */
    public function edit(): View
    {
        return view('gestao.configuracoes.edit', [
            'smtp' => Configuracao::mapa(SmtpRuntime::CHAVES),
            'configurado' => SmtpRuntime::configurado(),
            'jitsi' => Configuracao::mapa(JitsiToken::CHAVES),
            'jitsiConfigurado' => JitsiToken::configurado(),
        ]);
    }

    /** Salva as configurações de SMTP. */
    public function update(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'smtp_host' => ['nullable', 'string', 'max:255'],
            'smtp_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'smtp_encryption' => ['nullable', 'in:tls,ssl'],
            'smtp_username' => ['nullable', 'string', 'max:255'],
            'smtp_password' => ['nullable', 'string', 'max:255'],
            'smtp_from_address' => ['nullable', 'email', 'max:255'],
            'smtp_from_name' => ['nullable', 'string', 'max:255'],
        ], [], [
            'smtp_host' => 'servidor',
            'smtp_port' => 'porta',
            'smtp_from_address' => 'e-mail remetente',
        ]);

        foreach (SmtpRuntime::CHAVES as $chave) {
            // A senha só é alterada quando um novo valor é digitado
            // (o formulário nunca devolve a senha atual).
            if ($chave === 'smtp_password') {
                if (filled($dados['smtp_password'] ?? null)) {
                    Configuracao::definir('smtp_password', $dados['smtp_password']);
                }
                continue;
            }

            Configuracao::definir($chave, $dados[$chave] ?? null);
        }

        return back()->with('sucesso', 'Configurações de e-mail salvas.');
    }

    /** Salva as configurações de videoconferência (Jitsi). */
    public function atualizarJitsi(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'jitsi_domain' => ['nullable', 'string', 'max:255'],
            'jitsi_app_id' => ['nullable', 'string', 'max:255'],
            'jitsi_app_secret' => ['nullable', 'string', 'max:255'],
        ], [], [
            'jitsi_domain' => 'domínio',
            'jitsi_app_id' => 'app id',
            'jitsi_app_secret' => 'segredo',
        ]);

        Configuracao::definir('jitsi_domain', $dados['jitsi_domain'] ?? null);
        Configuracao::definir('jitsi_app_id', $dados['jitsi_app_id'] ?? null);

        // Segredo só é alterado quando um novo valor é digitado.
        if (filled($dados['jitsi_app_secret'] ?? null)) {
            Configuracao::definir('jitsi_app_secret', $dados['jitsi_app_secret']);
        }

        return back()->with('sucesso', 'Configurações de videoconferência salvas.');
    }

    /** Envia um e-mail de teste com as configurações atuais. */
    public function testar(Request $request): RedirectResponse
    {
        $dados = $request->validate(
            ['email_teste' => ['required', 'email']],
            [],
            ['email_teste' => 'e-mail de teste']
        );

        if (! SmtpRuntime::aplicar()) {
            return back()->with('erro', 'Configure o servidor SMTP antes de enviar um teste.');
        }

        try {
            Mail::raw(
                'E-mail de teste da Plataforma de Treinamentos (CISCOPAR). '
                .'Se você recebeu esta mensagem, o SMTP está funcionando corretamente.',
                fn ($m) => $m->to($dados['email_teste'])
                    ->subject('Teste de e-mail · Plataforma de Treinamentos')
            );
        } catch (\Throwable $e) {
            return back()->with('erro', 'Falha ao enviar: '.$e->getMessage());
        }

        return back()->with('sucesso', 'E-mail de teste enviado para '.$dados['email_teste'].'.');
    }
}
