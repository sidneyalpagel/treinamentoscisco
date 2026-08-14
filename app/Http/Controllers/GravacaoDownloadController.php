<?php

namespace App\Http\Controllers;

use App\Models\Configuracao;
use App\Models\Gravacao;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GravacaoDownloadController extends Controller
{
    /**
     * Faz o streaming da gravação a partir do serviço de arquivos do Jibri
     * (o portal é o "porteiro" — o arquivo continua armazenado no Jibri).
     * Acesso via link assinado (enviado por e-mail ao gestor).
     */
    public function __invoke(Gravacao $gravacao): StreamedResponse
    {
        $base = rtrim((string) Configuracao::valor('jibri_base_url'), '/');
        $secret = (string) Configuracao::valor('gravacao_secret');

        abort_if(blank($base) || blank($secret), 503, 'Armazenamento de gravações não configurado.');

        $origem = $base.'/'.ltrim($gravacao->arquivo, '/').'?k='.urlencode($secret);

        // Timeout curto para não travar quando o Jibri estiver fora do ar.
        $ctx = stream_context_create(['http' => ['timeout' => 15, 'method' => 'GET']]);
        $stream = @fopen($origem, 'rb', false, $ctx);
        abort_if($stream === false, 502, 'Não foi possível obter a gravação do servidor.');

        $nome = $gravacao->nomeDownload();

        return response()->streamDownload(function () use ($stream) {
            while (! feof($stream)) {
                echo fread($stream, 262144);
                flush();
            }
            fclose($stream);
        }, $nome, [
            'Content-Type' => 'video/mp4',
        ]);
    }
}
