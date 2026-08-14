<?php

namespace App\Support;

use App\Models\Configuracao;

/**
 * Aplica as configurações de SMTP (cadastradas pelo Admin no portal) ao
 * mailer do Laravel em tempo de execução, no lugar do .env.
 */
class SmtpRuntime
{
    /** Chaves de configuração de e-mail. */
    public const CHAVES = [
        'smtp_host',
        'smtp_port',
        'smtp_encryption',
        'smtp_username',
        'smtp_password',
        'smtp_from_address',
        'smtp_from_name',
    ];

    /** Há SMTP configurado (ao menos o host)? */
    public static function configurado(): bool
    {
        return filled(Configuracao::valor('smtp_host'));
    }

    /**
     * Injeta as credenciais no config('mail'). Retorna false se não houver
     * SMTP configurado (mantém o mailer padrão do .env, ex.: 'log').
     */
    public static function aplicar(): bool
    {
        if (! static::configurado()) {
            return false;
        }

        $c = Configuracao::mapa(self::CHAVES);
        $enc = $c['smtp_encryption'] ?? null;

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => $c['smtp_host'],
            'mail.mailers.smtp.port' => (int) ($c['smtp_port'] ?: 587),
            'mail.mailers.smtp.username' => $c['smtp_username'] ?: null,
            'mail.mailers.smtp.password' => $c['smtp_password'] ?: null,
            // ssl = TLS implícito (porta 465); tls = STARTTLS (porta 587)
            'mail.mailers.smtp.scheme' => $enc === 'ssl' ? 'smtps' : null,
            'mail.mailers.smtp.encryption' => $enc === 'tls' ? 'tls' : null,
            'mail.from.address' => $c['smtp_from_address'] ?: 'nao-responder@ciscopar.com.br',
            'mail.from.name' => $c['smtp_from_name'] ?: config('app.name'),
        ]);

        return true;
    }
}
