<?php

namespace Tests\Feature;

use App\Models\Configuracao;
use App\Models\Gravacao;
use App\Models\Treinamento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class GravacaoDownloadTest extends TestCase
{
    use RefreshDatabase;

    private function gravacao(): Gravacao
    {
        $treinamento = Treinamento::factory()->comSala()->create();

        return $treinamento->gravacoes()->create([
            'arquivo' => 'sessao/gravacao.mp4',
            'gravado_em' => now(),
        ]);
    }

    public function test_download_sem_assinatura_e_proibido(): void
    {
        $gravacao = $this->gravacao();

        $this->get(route('gravacoes.download', $gravacao))->assertForbidden();
    }

    public function test_download_com_assinatura_mas_sem_config_retorna_503(): void
    {
        $gravacao = $this->gravacao();

        $url = URL::temporarySignedRoute('gravacoes.download', now()->addMinutes(5), ['gravacao' => $gravacao->id]);

        $this->get($url)->assertStatus(503);
    }
}
