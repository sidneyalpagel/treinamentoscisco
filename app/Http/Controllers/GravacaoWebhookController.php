<?php

namespace App\Http\Controllers;

use App\Mail\GravacaoDisponivel;
use App\Models\Configuracao;
use App\Models\Treinamento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class GravacaoWebhookController extends Controller
{
    /**
     * Recebe do finalize.sh do Jibri o aviso de gravação pronta:
     * registra a Gravacao e envia ao gestor um link de download por e-mail.
     */
    public function store(Request $request): JsonResponse
    {
        $secret = (string) Configuracao::valor('gravacao_secret');
        abort_if(
            blank($secret) || ! hash_equals($secret, (string) $request->bearerToken()),
            401,
            'Não autorizado.'
        );

        $dados = $request->validate([
            'sala' => ['required', 'string', 'max:255'],
            'arquivo' => ['required', 'string', 'max:1024'],
            'tamanho' => ['nullable', 'integer', 'min:0'],
            'duracao' => ['nullable', 'integer', 'min:0'],
        ]);

        $treinamento = Treinamento::where('sala_codigo', $dados['sala'])->first();

        if (! $treinamento) {
            // Sala sem treinamento correspondente — aceita mas não registra.
            return response()->json(['ok' => false, 'motivo' => 'sala não encontrada'], 202);
        }

        $gravacao = $treinamento->gravacoes()->create([
            'arquivo' => $dados['arquivo'],
            'tamanho' => $dados['tamanho'] ?? null,
            'duracao_seg' => $dados['duracao'] ?? null,
            'gravado_em' => now(),
        ]);

        $gestor = $treinamento->user;
        if ($gestor && filled($gestor->email)) {
            $url = URL::temporarySignedRoute('gravacoes.download', now()->addDays(7), ['gravacao' => $gravacao->id]);

            try {
                Mail::to($gestor->email)->send(new GravacaoDisponivel($gravacao, $url));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return response()->json(['ok' => true, 'gravacao' => $gravacao->id]);
    }
}
