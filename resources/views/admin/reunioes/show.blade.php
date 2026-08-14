@extends('layouts.admin')

@section('titulo', $reuniao->titulo)

@section('conteudo')
    <div class="mb-6">
        <a href="{{ route('admin.reunioes.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-brand-700 mb-3">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            Voltar para a lista
        </a>
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ $reuniao->titulo }}</h1>
                <p class="text-sm text-slate-500 mt-1">
                    {{ $reuniao->data_inicio->translatedFormat('d \d\e F \d\e Y, H:i') }}
                    @if ($reuniao->data_fim) — {{ $reuniao->data_fim->translatedFormat('H:i') }} @endif
                </p>
            </div>
            <a href="{{ route('admin.reunioes.edit', $reuniao) }}"
               class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                Editar
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-semibold text-slate-800 mb-3">Descrição / pauta</h2>
                @if ($reuniao->descricao)
                    <div class="prose prose-sm max-w-none text-slate-600 whitespace-pre-line">{{ $reuniao->descricao }}</div>
                @else
                    <p class="text-sm text-slate-400">Nenhuma descrição informada.</p>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-semibold text-slate-800 mb-1">Videoconferência</h2>
                <p class="text-sm text-slate-500 mb-4">Você entra como <strong>moderador</strong> — grava e controla a reunião.</p>
                <a href="{{ route('admin.reunioes.entrar', $reuniao) }}" target="_blank"
                   class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-brand-700 px-3.5 py-2 text-sm font-semibold text-white hover:bg-brand-800 transition-colors mb-4">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                    Entrar na sala
                </a>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Link dos participantes</label>
                <div class="flex gap-2">
                    <input id="link-reuniao" type="text" readonly value="{{ route('reuniao.publica', $reuniao->sala_codigo) }}"
                           class="flex-1 min-w-0 rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-xs text-slate-600">
                    <button type="button" onclick="copiarLinkReuniao(this)" class="shrink-0 rounded-lg border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50">Copiar</button>
                </div>
                <p class="mt-1.5 text-xs text-slate-500">Compartilhe este link único com os participantes.</p>

                @if ($reuniao->gravacoes->isNotEmpty())
                    <div class="mt-5 pt-4 border-t border-slate-100">
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Gravações</div>
                        <ul class="space-y-2">
                            @foreach ($reuniao->gravacoes as $g)
                                <li class="flex items-center justify-between gap-2 text-sm">
                                    <span class="text-slate-600">
                                        {{ optional($g->gravado_em)->translatedFormat('d/m/Y H:i') ?? 'Gravação' }}
                                        @if ($g->tamanho_legivel)<span class="text-slate-400"> · {{ $g->tamanho_legivel }}</span>@endif
                                    </span>
                                    <a href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('gravacoes.download', now()->addDays(7), ['gravacao' => $g->id]) }}"
                                       class="shrink-0 inline-flex items-center gap-1 text-brand-700 hover:text-brand-800 font-medium">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                        Baixar
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <script>
                    function copiarLinkReuniao(btn) {
                        var campo = document.getElementById('link-reuniao');
                        navigator.clipboard.writeText(campo.value).then(function () {
                            var txt = btn.textContent; btn.textContent = 'Copiado!';
                            setTimeout(function () { btn.textContent = txt; }, 1500);
                        });
                    }
                </script>
            </div>

            <div class="rounded-xl border border-red-200 bg-white p-6 shadow-sm">
                <h2 class="font-semibold text-slate-800 mb-1">Remover reunião</h2>
                <p class="text-sm text-slate-500 mb-4">Esta ação não poderá ser desfeita.</p>
                <form method="POST" action="{{ route('admin.reunioes.destroy', $reuniao) }}"
                      onsubmit="return confirm('Remover a reunião “{{ addslashes($reuniao->titulo) }}”?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-red-300 bg-red-50 px-3.5 py-2 text-sm font-medium text-red-700 hover:bg-red-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165"/></svg>
                        Remover
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
