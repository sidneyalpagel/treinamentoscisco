<?php

namespace Tests\Feature;

use App\Mail\GravacaoDisponivel;
use App\Models\Configuracao;
use App\Models\Gravacao;
use App\Models\Reuniao;
use App\Models\Treinamento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class GravacaoWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_sem_token_e_rejeitado(): void
    {
        Configuracao::definir('gravacao_secret', 'sec123');

        $this->postJson(route('webhooks.gravacao'), ['sala' => 'x', 'arquivo' => 'a.mp4'])
            ->assertUnauthorized();
    }

    public function test_webhook_registra_gravacao_de_treinamento_e_notifica_gestor(): void
    {
        Mail::fake();
        Configuracao::definir('gravacao_secret', 'sec123');
        $treinamento = Treinamento::factory()->comSala()->create();

        $resp = $this->withToken('sec123')->postJson(route('webhooks.gravacao'), [
            'sala' => $treinamento->sala_codigo,
            'arquivo' => 'sessao/gravacao.mp4',
            'tamanho' => 1000,
        ]);

        $resp->assertOk()->assertJson(['ok' => true]);
        $this->assertDatabaseCount('gravacoes', 1);

        $gravacao = Gravacao::first();
        $this->assertTrue($gravacao->gravavel->is($treinamento));

        Mail::assertSent(GravacaoDisponivel::class, fn ($m) => $m->hasTo($treinamento->user->email));
    }

    public function test_webhook_registra_gravacao_de_reuniao_polimorfica(): void
    {
        Configuracao::definir('gravacao_secret', 'sec123');
        $reuniao = Reuniao::factory()->create();

        $this->withToken('sec123')->postJson(route('webhooks.gravacao'), [
            'sala' => $reuniao->sala_codigo,
            'arquivo' => 'sessao/gravacao.mp4',
        ])->assertOk();

        $gravacao = Gravacao::first();
        $this->assertInstanceOf(Reuniao::class, $gravacao->gravavel);
        $this->assertTrue($gravacao->gravavel->is($reuniao));
    }

    public function test_webhook_de_sala_inexistente_nao_registra(): void
    {
        Configuracao::definir('gravacao_secret', 'sec123');

        $this->withToken('sec123')->postJson(route('webhooks.gravacao'), [
            'sala' => 'sala-que-nao-existe',
            'arquivo' => 'a.mp4',
        ])->assertStatus(202);

        $this->assertDatabaseCount('gravacoes', 0);
    }
}
