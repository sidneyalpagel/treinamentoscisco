<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Link da reunião</title>
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
                            <p style="margin:0 0 16px; font-size:16px;">Olá, {{ $nome }}!</p>
                            <p style="margin:0 0 16px; font-size:14px; line-height:1.6;">
                                Sua inscrição no treinamento <strong>{{ $treinamento->titulo }}</strong> foi confirmada.
                                Ele é <strong>online</strong> — participe pelo link abaixo, direto no navegador.
                            </p>
                            <p style="margin:0 0 16px; font-size:14px; line-height:1.6;">
                                Data: <strong>{{ $treinamento->data_inicio->translatedFormat('d \d\e F \d\e Y, H:i') }}</strong>
                            </p>
                            <p style="margin:24px 0; text-align:center;">
                                <a href="{{ $url }}" style="background:#1d4ed8; color:#ffffff; text-decoration:none; font-size:14px; font-weight:bold; padding:12px 28px; border-radius:8px; display:inline-block;">
                                    Entrar na reunião
                                </a>
                            </p>
                            <p style="margin:0 0 8px; font-size:12px; color:#64748b; line-height:1.6;">
                                Guarde este e-mail — o mesmo link vale para o dia do treinamento. Se o botão não funcionar, copie e cole no navegador:
                            </p>
                            <p style="margin:0; font-size:12px; word-break:break-all;">
                                <a href="{{ $url }}" style="color:#1d4ed8;">{{ $url }}</a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 32px; background:#f8fafc; border-top:1px solid #e2e8f0; font-size:11px; color:#94a3b8;">
                            Este link é pessoal da sua inscrição — evite compartilhar.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
