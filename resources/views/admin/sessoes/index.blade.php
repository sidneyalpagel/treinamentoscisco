@extends('layouts.admin')

@section('titulo', 'Sessões · '.$treinamento->titulo)

@section('conteudo')
    <div class="mb-6">
        <a href="{{ route('admin.treinamentos.show', $treinamento) }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-brand-700 mb-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            Voltar ao treinamento
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Sessões e presença</h1>
        <p class="text-sm text-slate-500">{{ $treinamento->titulo }} · {{ $totalConfirmados }} participante(s) confirmado(s)</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Lista de sessões --}}
        <div class="lg:col-span-2 space-y-4">
            @forelse ($treinamento->sessoes as $sessao)
                <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <div class="flex flex-wrap items-start justify-between gap-3 p-5">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-slate-800">{{ $sessao->nome_exibicao }}</h3>
                                @if ($sessao->presenca_aberta)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20 px-2 py-0.5 text-xs font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Check-in aberto
                                    </span>
                                @endif
                            </div>
                            <div class="mt-1 text-sm text-slate-500">
                                {{ $sessao->data->translatedFormat('l, d/m/Y') }} · {{ $sessao->horario }}
                            </div>
                            <div class="mt-2 text-sm text-slate-600">
                                <span class="font-semibold text-brand-700">{{ $sessao->presencas_count }}</span> / {{ $totalConfirmados }} presentes
                            </div>
                        </div>
                        <form method="POST" action="{{ route('admin.sessoes.destroy', $sessao) }}"
                              onsubmit="return confirm('Remover esta sessão e todas as presenças registradas?');">
                            @csrf @method('DELETE')
                            <button type="submit" title="Remover sessão" class="grid place-items-center w-8 h-8 rounded-md text-slate-400 hover:bg-red-50 hover:text-red-600">
                                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165"/></svg>
                            </button>
                        </form>
                    </div>

                    {{-- Link de check-in --}}
                    <div class="border-t border-slate-100 bg-slate-50 px-5 py-3 flex flex-wrap items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="text-xs text-slate-500 mb-0.5">Link de auto check-in (código <span class="font-mono font-semibold text-slate-700">{{ $sessao->codigo }}</span>)</div>
                            <div class="font-mono text-xs text-slate-600 truncate">{{ route('presenca.form', $sessao->codigo) }}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('admin.sessoes.chamada', $sessao) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors {{ $sessao->presenca_aberta ? 'bg-red-50 text-red-700 hover:bg-red-100' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}">
                                    {{ $sessao->presenca_aberta ? 'Fechar check-in' : 'Abrir check-in' }}
                                </button>
                            </form>
                            <a href="{{ route('admin.sessoes.presenca', $sessao) }}"
                               class="inline-flex items-center gap-1.5 rounded-lg bg-brand-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-brand-800 transition-colors">
                                Lista de presença
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-xl border border-dashed border-slate-300 bg-white py-12 text-center text-slate-400">
                    <svg class="w-10 h-10 mx-auto mb-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                    Nenhuma sessão cadastrada. Adicione a primeira ao lado.
                </div>
            @endforelse
        </div>

        {{-- Adicionar sessão --}}
        <div>
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm sticky top-6">
                <h2 class="font-semibold text-slate-800 mb-4">Adicionar sessão</h2>
                @php($campo = 'w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none')
                <form method="POST" action="{{ route('admin.sessoes.store', $treinamento) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="titulo" class="block text-xs font-medium text-slate-600 mb-1">Título (opcional)</label>
                        <input id="titulo" name="titulo" value="{{ old('titulo') }}" class="{{ $campo }} @error('titulo') border-red-400 @enderror" placeholder="Ex.: Dia 1 - Manhã">
                        @error('titulo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="data" class="block text-xs font-medium text-slate-600 mb-1">Data <span class="text-red-500">*</span></label>
                        <input id="data" name="data" type="date" value="{{ old('data', $treinamento->data_inicio->format('Y-m-d')) }}" required class="{{ $campo }} @error('data') border-red-400 @enderror">
                        @error('data') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="hora_inicio" class="block text-xs font-medium text-slate-600 mb-1">Início <span class="text-red-500">*</span></label>
                            <input id="hora_inicio" name="hora_inicio" type="time" value="{{ old('hora_inicio', '09:00') }}" required class="{{ $campo }} @error('hora_inicio') border-red-400 @enderror">
                            @error('hora_inicio') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="hora_fim" class="block text-xs font-medium text-slate-600 mb-1">Término</label>
                            <input id="hora_fim" name="hora_fim" type="time" value="{{ old('hora_fim') }}" class="{{ $campo }} @error('hora_fim') border-red-400 @enderror">
                            @error('hora_fim') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition-colors">
                        Adicionar sessão
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
