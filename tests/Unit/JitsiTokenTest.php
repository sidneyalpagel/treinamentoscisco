<?php

namespace Tests\Unit;

use App\Models\Configuracao;
use App\Support\JitsiToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JitsiTokenTest extends TestCase
{
    use RefreshDatabase;

    private function configurar(): void
    {
        Configuracao::definir('jitsi_domain', 'meet.teste');
        Configuracao::definir('jitsi_app_id', 'ciscopar');
        Configuracao::definir('jitsi_app_secret', 'segredo-super');
    }

    public function test_configurado_reflete_a_config(): void
    {
        $this->assertFalse(JitsiToken::configurado());
        $this->configurar();
        $this->assertTrue(JitsiToken::configurado());
    }

    public function test_url_gera_jwt_com_claims_e_assinatura_valida(): void
    {
        $this->configurar();

        $url = JitsiToken::url('sala-x', ['name' => 'Fulano', 'email' => 'f@x.com'], moderador: true);

        $this->assertStringStartsWith('https://meet.teste/sala-x?jwt=', $url);

        $jwt = explode('jwt=', $url)[1];
        [$h, $p, $s] = explode('.', $jwt);
        $payload = json_decode(base64_decode(strtr($p, '-_', '+/')), true);

        $this->assertSame('jitsi', $payload['aud']);
        $this->assertSame('ciscopar', $payload['iss']);
        $this->assertSame('meet.teste', $payload['sub']);
        $this->assertSame('sala-x', $payload['room']);
        $this->assertTrue($payload['context']['user']['moderator']);
        $this->assertSame('Fulano', $payload['context']['user']['name']);
        $this->assertGreaterThan(time(), $payload['exp']);

        // A assinatura deve conferir com o segredo (o Jitsi valida assim).
        $esperada = rtrim(strtr(base64_encode(hash_hmac('sha256', "$h.$p", 'segredo-super', true)), '+/', '-_'), '=');
        $this->assertSame($esperada, $s);
    }

    public function test_participante_nao_e_moderador_e_nao_leva_nome(): void
    {
        $this->configurar();

        $url = JitsiToken::url('sala-y', [], moderador: false);
        $jwt = explode('jwt=', $url)[1];
        $payload = json_decode(base64_decode(strtr(explode('.', $jwt)[1], '-_', '+/')), true);

        $this->assertFalse($payload['context']['user']['moderator']);
        $this->assertArrayNotHasKey('name', $payload['context']['user']);
    }
}
