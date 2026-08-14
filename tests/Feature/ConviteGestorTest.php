<?php

namespace Tests\Feature;

use App\Mail\ConviteGestor;
use App\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ConviteGestorTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cadastra_gestor_sem_senha_e_envia_convite(): void
    {
        Mail::fake();
        $admin = User::factory()->admin()->create();
        $area = Area::create(['nome' => 'Saúde', 'ativo' => true]);

        $resp = $this->actingAs($admin)->post(route('gestao.usuarios.store'), [
            'name' => 'Novo Gestor',
            'email' => 'novo@ciscopar.com.br',
            'role' => 'gestor',
            'area_id' => $area->id,
            'ativo' => '1',
        ]);

        $resp->assertRedirect(route('gestao.usuarios.index'));

        $user = User::where('email', 'novo@ciscopar.com.br')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->pendenteAtivacao());

        Mail::assertSent(ConviteGestor::class, fn ($m) => $m->hasTo('novo@ciscopar.com.br'));
    }

    public function test_login_bloqueado_enquanto_convite_pendente(): void
    {
        User::factory()->pendente()->create([
            'email' => 'pendente@ciscopar.com.br',
            'password' => 'segredo123',
        ]);

        $resp = $this->post(route('login.store'), [
            'email' => 'pendente@ciscopar.com.br',
            'password' => 'segredo123',
        ]);

        $resp->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_ativacao_define_senha_ativa_e_autentica(): void
    {
        $user = User::factory()->pendente()->create();
        $token = $user->convite_token;

        $resp = $this->post(route('ativar.store', $token), [
            'password' => 'novaSenha123',
            'password_confirmation' => 'novaSenha123',
        ]);

        $resp->assertRedirect();
        $user->refresh();

        $this->assertFalse($user->pendenteAtivacao());
        $this->assertTrue(Hash::check('novaSenha123', $user->password));
        $this->assertAuthenticatedAs($user);
    }

    public function test_token_de_ativacao_invalido_nao_autentica(): void
    {
        $resp = $this->post(route('ativar.store', 'token-que-nao-existe-mas-tem-tamanho-suficiente-1234567890'), [
            'password' => 'novaSenha123',
            'password_confirmation' => 'novaSenha123',
        ]);

        $resp->assertRedirect(route('ativar.form', 'token-que-nao-existe-mas-tem-tamanho-suficiente-1234567890'));
        $this->assertGuest();
    }
}
