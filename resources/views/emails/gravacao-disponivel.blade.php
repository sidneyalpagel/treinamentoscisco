<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gravação disponível</title>
</head>
<body style="margin:0; padding:0; background:#f1f5f9; font-family:Arial, Helvetica, sans-serif; color:#334155;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="max-width:560px; background:#ffffff; border-radius:12px; overflow:hidden; border:1px solid #e2e8f0;">
                    <tr>
                        <td style="background:#1e3a8a; padding:24px 32px; color:#ffffff; font-size:18px; font-weight:bold;">
                            Plataforma de Treinamentos
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 16px; font-size:16px;">A gravação do seu treinamento está pronta.</p>
                            <p style="margin:0 0 16px; font-size:14px; line-height:1.6;">
                                {{ $gravacao->tituloOrigem() }}<br>
                                @if ($gravacao->gravado_em)
                                    Gravado em: {{ $gravacao->gravado_em->translatedFormat('d/m/Y H:i') }}<br>
                                @endif
                                @if ($gravacao->tamanho_legivel)
                                    Tamanho: {{ $gravacao->tamanho_legivel }}
                                @endif
                            </p>
                            <p style="margin:24px 0; text-align:center;">
                                <a href="{{ $url }}" style="background:#1d4ed8; color:#ffffff; text-decoration:none; font-size:14px; font-weight:bold; padding:12px 28px; border-radius:8px; display:inline-block;">
                                    Baixar gravação
                                </a>
                            </p>
                            <p style="margin:0; font-size:12px; color:#64748b; line-height:1.6;">
                                Este link de download é válido por 7 dias e é pessoal — não o compartilhe.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
