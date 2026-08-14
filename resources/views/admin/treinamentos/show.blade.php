@extends('layouts.admin')

@section('titulo', $treinamento->titulo)

@section('conteudo')
    <div class="mb-6">
        <a href="{{ route('admin.treinamentos.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-brand-700 mb-3">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            Voltar para a lista
        </a>
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-slate-800">{{ $treinamento->titulo }}</h1>
                    <x-status-badge :status="$treinamento->status" :label="$treinamento->status_label" />
                </div>
                <p class="text-sm text-slate-500 mt-1">{{ $treinamento->modalidade_label }} · {{ $treinamento->data_inicio->translatedFormat('d \d\e F \d\e Y, H:i') }}</p>
            </div>
            <div class="flex items-center gap-2">
                @if ($treinamento->estaPublicado())
                    <a href="{{ route('treinamentos.show', $treinamento) }}" target="_blank"
                       class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                        Ver página pública
                    </a>
                @endif
                <a href="{{ route('admin.treinamentos.edit', $treinamento) }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-brand-700 px-3.5 py-2 text-sm font-semibold text-white hover:bg-brand-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                    Editar
                </a>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-semibold text-slate-800 mb-3">Descrição</h2>
                @if ($treinamento->descricao)
                    <div class="prose prose-sm max-w-none text-slate-600 whitespace-pre-line">{{ $treinamento->descricao }}</div>
                @else
                    <p class="text-sm text-slate-400">Nenhuma descrição informada.</p>
                @endif

                @if ($treinamento->publico_alvo)
                    <div class="mt-5 pt-5 border-t border-slate-100">
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Público-alvo</div>
                        <div class="mt-1 text-sm text-slate-700">{{ $treinamento->publico_alvo }}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-semibold text-slate-800 mb-4">Detalhes</h2>
                <dl class="space-y-3.5 text-sm">
                    @php
                        $linhas = [
                            ['Instrutor(a)', $treinamento->instrutor],
                            ['Carga horária', $treinamento->carga_horaria ? $treinamento->carga_horaria.'h' : null],
                            ['Local', $treinamento->local],
                            ['Término', optional($treinamento->data_fim)->translatedFormat('d/m/Y H:i')],
                            ['Vagas', $treinamento->vagas ?? 'Ilimitado'],
                            ['Inscrições até', optional($treinamento->inscricoes_ate)->translatedFormat('d/m/Y')],
                        ];
                    @endphp
                    @foreach ($linhas as [$rotulo, $valor])
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-slate-500">{{ $rotulo }}</dt>
                            <dd class="text-slate-800 font-medium text-right">{{ $valor ?: '—' }}</dd>
                        </div>
                    @endforeach
                </dl>
                <div class="mt-4 pt-4 border-t border-slate-100 space-y-2">
                    <div class="flex items-center gap-2 text-sm {{ $treinamento->inscricoesAbertas() ? 'text-emerald-700' : 'text-slate-400' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        Inscrições {{ $treinamento->inscricoesAbertas() ? 'abertas' : 'indisponíveis' }}
                    </div>
                    <div class="flex items-center gap-2 text-sm {{ $treinamento->gera_certificado ? 'text-emerald-700' : 'text-slate-400' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        Emite certificado
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-semibold text-slate-800 mb-4">Inscrições</h2>
                @php
                    $totalInscritos = $treinamento->inscricoes()->count();
                    $confirmados = $treinamento->totalConfirmadas();
                @endphp
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="rounded-lg bg-slate-50 p-3 text-center">
                        <div class="text-2xl font-bold text-slate-800">{{ $totalInscritos }}</div>
                        <div class="text-xs text-slate-500">Total</div>
                    </div>
                    <div class="rounded-lg bg-emerald-50 p-3 text-center">
                        <div class="text-2xl font-bold text-emerald-700">{{ $confirmados }}</div>
                        <div class="text-xs text-emerald-600">Confirmadas</div>
                    </div>
                </div>
                @if (! is_null($treinamento->vagas))
                    <p class="text-sm text-slate-500 mb-4">{{ $treinamento->vagasRestantes() }} de {{ $treinamento->vagas }} vagas disponíveis.</p>
                @endif
                <a href="{{ route('admin.inscricoes.index', ['treinamento' => $treinamento->id]) }}"
                   class="w-full inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                    Gerenciar inscrições
                </a>
            </div>

            <div class="rounded-xl border border-red-200 bg-white p-6 shadow-sm">
                <h2 class="font-semibold text-slate-800 mb-1">Remover treinamento</h2>
                <p class="text-sm text-slate-500 mb-4">Esta ação não poderá ser desfeita.</p>
                <form method="POST" action="{{ route('admin.treinamentos.destroy', $treinamento) }}"
                      onsubmit="return confirm('Remover o treinamento “{{ addslashes($treinamento->titulo) }}”?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-red-300 bg-red-50 px-3.5 py-2 text-sm font-medium text-red-700 hover:bg-red-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165"/></svg>
                        Remover
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
