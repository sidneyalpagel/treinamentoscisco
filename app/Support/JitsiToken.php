<?php

namespace App\Support;

use App\Models\Configuracao;

/**
 * Assina tokens JWT (HS256) para o Jitsi self-hosted e monta a URL da sala.
 * As credenciais (domínio, app id e segredo) são cadastradas pelo Admin em
 * Configurações e devem casar com o jitsi.conf do servidor Jitsi.
 */
class JitsiToken
{
    public const CHAVES = ['jitsi_domain', 'jitsi_app_id', 'jitsi_app_secret'];

    /** Há videoconferência configurada (domínio + segredo)? */
    public static function configurado(): bool
    {
        return filled(Configuracao::valor('jitsi_domain'))
            && filled(Configuracao::valor('jitsi_app_secret'));
    }

    public static function dominio(): ?string
    {
        return Configuracao::valor('jitsi_domain');
    }

    /**
     * URL da sala com um JWT assinado.
     *
     * @param  array{name?:string,email?:string}  $usuario
     */
    public static function url(string $sala, array $usuario, bool $moderador, int $ttl = 14400): string
    {
        $dominio = (string) Configuracao::valor('jitsi_domain');
        $appId = (string) (Configuracao::valor('jitsi_app_id') ?: 'ciscopar');
        $secret = (string) Configuracao::valor('jitsi_app_secret');

        $token = static::assinar($dominio, $appId, $secret, $sala, $usuario, $moderador, $ttl);

        return sprintf('https://%s/%s?jwt=%s', $dominio, rawurlencode($sala), $token);
    }

    private static function assinar(string $dominio, string $appId, string $secret, string $sala, array $usuario, bool $moderador, int $ttl): string
    {
        $b64 = static fn (string $bin): string => rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');

        $user = ['moderator' => $moderador];
        if (! empty($usuario['name'])) {
            $user['name'] = $usuario['name'];
        }
        if (! empty($usuario['email'])) {
            $user['email'] = $usuario['email'];
        }

        $agora = time();
        $header = $b64(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload = $b64(json_encode([
            'aud' => 'jitsi',
            'iss' => $appId,
            'sub' => $dominio,
            'room' => $sala,
            'iat' => $agora,
            'nbf' => $agora - 5,
            'exp' => $agora + $ttl,
            'context' => ['user' => $user],
        ]));

        $assinatura = $b64(hash_hmac('sha256', "{$header}.{$payload}", $secret, true));

        return "{$header}.{$payload}.{$assinatura}";
    }
}
