<?php

namespace Tests\Feature;

use App\Models\Configuracao;
use App\Models\Reuniao;
use App\Models\Treinamento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalaEReuniaoTest extends TestCase
{
    use RefreshDatabase;

    private function configurarJitsi(): void
    {
        Configuracao::definir('jitsi_domain', 'meet.teste');
        Configuracao::definir('jitsi_app_id', 'ciscopar');
        Configuracao::definir('jitsi_app_secret', 'segredo-super');
    }

    public function test_gestor_cria_sala_em_treinamento_online(): void
    {
        $this->configurarJitsi();
        $gestor = User::factory()->create();
        $treinamento = Treinamento::factory()->create(['user_id' => $gestor->id]);

        $this->actingAs($gestor)->post(route('admin.sala.criar', $treinamento))->assertRedirect();

        $treinamento->refresh();
        $this->assertTrue($treinamento->temSala());
    }

    public function test_nao_cria_sala_em_treinamento_presencial(): void
    {
        $this->configurarJitsi();
        $gestor = User::factory()->create();
        $treinamento = Treinamento::factory()->presencial()->create(['user_id' => $gestor->id]);

        $this->actingAs($gestor)->post(route('admin.sala.criar', $treinamento));

        $this->assertFalse($treinamento->fresh()->temSala());
    }

    public function test_criar_treinamento_online_marcado_ja_cria_a_sala(): void
    {
        $this->configurarJitsi();
        $gestor = User::factory()->create();

        $resp = $this->actingAs($gestor)->post(route('admin.treinamentos.store'), [
            'titulo' => 'Treino Online',
            'modalidade' => 'online',
            'status' => 'publicado',
            'data_inicio' => now()->addDay()->format('Y-m-d\TH:i'),
            'criar_sala' => '1',
        ]);

        $treinamento = Treinamento::first();
        $this->assertNotNull($treinamento);
        $this->assertTrue($treinamento->temSala());
        $resp->assertRedirect(route('admin.treinamentos.show', $treinamento));
    }

    public function test_treinamento_presencial_nao_cria_sala_mesmo_marcado(): void
    {
        $this->configurarJitsi();
        $gestor = User::factory()->create();

        $this->actingAs($gestor)->post(route('admin.treinamentos.store'), [
            'titulo' => 'Treino Presencial',
            'modalidade' => 'presencial',
            'status' => 'rascunho',
            'data_inicio' => now()->addDay()->format('Y-m-d\TH:i'),
            'criar_sala' => '1',
        ]);

        $this->assertFalse(Treinamento::first()->temSala());
    }

    public function test_moderador_entra_na_sala_com_jwt(): void
    {
        $this->configurarJitsi();
        $gestor = User::factory()->create();
        $treinamento = Treinamento::factory()->comSala()->create(['user_id' => $gestor->id]);

        $resp = $this->actingAs($gestor)->get(route('admin.sala.entrar', $treinamento));

        $resp->assertRedirect();
        $location = $resp->headers->get('Location');
        $this->assertStringStartsWith('https://meet.teste/', $location);
        $this->assertStringContainsString('jwt=', $location);
    }

    public function test_participante_entra_por_link_publico(): void
    {
        $this->configurarJitsi();
        $treinamento = Treinamento::factory()->comSala()->create();

        $resp = $this->get(route('sala.publica', $treinamento->sala_codigo));

        $resp->assertRedirect();
        $this->assertStringContainsString('jwt=', $resp->headers->get('Location'));
    }

    public function test_gestor_cria_reuniao_com_sala_automatica(): void
    {
        $gestor = User::factory()->create();

        $resp = $this->actingAs($gestor)->post(route('admin.reunioes.store'), [
            'titulo' => 'Reunião de teste',
            'data_inicio' => now()->addDay()->format('Y-m-d\TH:i'),
        ]);

        $reuniao = Reuniao::first();
        $this->assertNotNull($reuniao);
        $this->assertNotEmpty($reuniao->sala_codigo);
        $this->assertSame($gestor->id, $reuniao->user_id);
        $resp->assertRedirect(route('admin.reunioes.show', $reuniao));
    }

    public function test_gestor_cria_reuniao_imediata_sem_data(): void
    {
        $gestor = User::factory()->create();

        $resp = $this->actingAs($gestor)->post(route('admin.reunioes.agora'), []);

        $reuniao = Reuniao::first();
        $this->assertNotNull($reuniao);
        $this->assertNotEmpty($reuniao->sala_codigo);
        $this->assertNotNull($reuniao->data_inicio);
        $this->assertSame($gestor->id, $reuniao->user_id);
        $resp->assertRedirect(route('admin.reunioes.show', $reuniao));
    }

    public function test_gestor_nao_acessa_reuniao_de_outro(): void
    {
        $dono = User::factory()->create();
        $outro = User::factory()->create();
        $reuniao = Reuniao::factory()->create(['user_id' => $dono->id]);

        $this->actingAs($outro)->get(route('admin.reunioes.show', $reuniao))->assertForbidden();
    }
}
