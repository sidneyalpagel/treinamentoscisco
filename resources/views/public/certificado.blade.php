<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Certificado · {{ $certificado->inscricao->nome }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            @page { size: A4 landscape; margin: 0; }
            body { background: #fff !important; }
            .folha { box-shadow: none !important; border-radius: 0 !important; margin: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-200 min-h-screen py-8 px-4">

    {{-- Barra de ações --}}
    <div class="no-print max-w-4xl mx-auto mb-6 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-600 hover:text-brand-700">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            Início
        </a>
        <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/></svg>
            Imprimir / Salvar PDF
        </button>
    </div>

    {{-- Folha do certificado --}}
    <div class="folha max-w-4xl mx-auto bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="p-2.5" style="background: linear-gradient(90deg, var(--color-brand-900), var(--color-brand-600));">
            <div class="border-2 border-white/30 rounded-md">
                <div class="bg-white px-10 py-12 sm:px-16 sm:py-16 text-center">

                    <div class="flex items-center justify-center gap-3 mb-8">
                        <span class="grid place-items-center w-12 h-12 rounded-lg bg-brand-800 text-white">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814"/></svg>
                        </span>
                        <span class="text-lg font-bold text-brand-800">Plataforma de Treinamentos</span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl font-bold tracking-[0.2em] text-brand-900 uppercase">Certificado</h1>
                    <div class="mx-auto mt-3 mb-10 h-1 w-20 rounded bg-brand-500"></div>

                    <p class="text-slate-500 text-lg">Certificamos que</p>
                    <p class="mt-3 text-3xl sm:text-4xl font-bold text-slate-800">{{ $certificado->inscricao->nome }}</p>

                    <p class="mt-6 text-slate-600 text-lg leading-relaxed max-w-2xl mx-auto">
                        participou do treinamento
                        <strong class="text-slate-800">“{{ $certificado->inscricao->treinamento->titulo }}”</strong>@if ($certificado->carga_horaria), com carga horária de <strong class="text-slate-800">{{ $certificado->carga_horaria }} horas</strong>@endif,
                        realizado em {{ $certificado->inscricao->treinamento->data_inicio->translatedFormat('d \d\e F \d\e Y') }}.
                    </p>

                    <div class="mt-12 flex flex-wrap items-end justify-between gap-6">
                        <div class="text-left text-sm text-slate-500">
                            <div>Emitido em {{ $certificado->emitido_em->translatedFormat('d/m/Y') }}</div>
                            @if ($certificado->inscricao->treinamento->instrutor)
                                <div class="mt-1">Instrutor(a): {{ $certificado->inscricao->treinamento->instrutor }}</div>
                            @endif
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-slate-400 uppercase tracking-wider">Código de validação</div>
                            <div class="font-mono font-bold text-brand-800 text-lg">{{ $certificado->codigo }}</div>
                            <div class="text-xs text-slate-400 mt-1">Valide em {{ route('certificados.validar') }}</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</body>
</html>
