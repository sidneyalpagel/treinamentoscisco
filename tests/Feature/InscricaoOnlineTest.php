<?php

namespace Tests\Feature;

use App\Mail\LinkReuniao;
use App\Models\Treinamento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InscricaoOnlineTest extends TestCase
{
    use RefreshDatabase;

    public function test_inscricao_em_online_com_sala_envia_link_por_email(): void
    {
        Mail::fake();
        $treinamento = Treinamento::factory()->comSala()->create([
            'status' => 'publicado',
            'permite_inscricao' => true,
            'inscricoes_ate' => now()->addDays(5),
        ]);

        $resp = $this->post(route('treinamentos.inscrever', $treinamento), [
            'nome' => 'Fulano de Tal',
            'email' => 'fulano@ciscopar.com.br',
        ]);

        $resp->assertRedirect(route('treinamentos.show', $treinamento));
        $this->assertDatabaseCount('inscricoes', 1);
        Mail::assertSent(LinkReuniao::class, fn ($m) => $m->hasTo('fulano@ciscopar.com.br'));
    }

    public function test_inscricao_em_presencial_nao_envia_link(): void
    {
        Mail::fake();
        $treinamento = Treinamento::factory()->presencial()->create([
            'status' => 'publicado',
            'permite_inscricao' => true,
        ]);

        $this->post(route('treinamentos.inscrever', $treinamento), [
            'nome' => 'Fulano de Tal',
            'email' => 'fulano@ciscopar.com.br',
        ]);

        Mail::assertNothingSent();
    }
}
