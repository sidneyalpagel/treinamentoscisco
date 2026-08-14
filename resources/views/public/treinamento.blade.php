@extends('layouts.public')

@section('titulo', $treinamento->titulo)

@section('conteudo')
    {{-- Cabeçalho --}}
    <section class="bg-brand-800 text-white">
        <div class="mx-auto max-w-7xl px-4 py-12">
            <nav class="text-sm text-brand-200 mb-3">
                <a href="{{ route('home') }}" class="hover:text-white">Início</a>
                <span class="mx-1.5 opacity-60">/</span>
                <a href="{{ route('agenda') }}" class="hover:text-white">Agenda</a>
                <span class="mx-1.5 opacity-60">/</span>
                <span class="text-white/90">{{ \Illuminate\Support\Str::limit($treinamento->titulo, 40) }}</span>
            </nav>
            <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-medium ring-1 ring-white/15">{{ $treinamento->modalidade_label }}</span>
            <h1 class="mt-3 text-3xl lg:text-4xl font-bold">{{ $treinamento->titulo }}</h1>
            <div class="mt-4 flex flex-wrap gap-x-6 gap-y-2 text-sm text-brand-100/90">
                <span class="inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75"/></svg>
                    {{ $treinamento->data_inicio->translatedFormat('d \d\e F \d\e Y') }}
                </span>
                <span class="inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    {{ $treinamento->data_inicio->translatedFormat('H:i') }}@if ($treinamento->carga_horaria) · {{ $treinamento->carga_horaria }}h @endif
                </span>
                @if ($treinamento->local)
                    <span class="inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                        {{ $treinamento->local }}
                    </span>
                @endif
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-12 grid gap-8 lg:grid-cols-3">
        {{-- Conteúdo --}}
        <div class="lg:col-span-2 space-y-8">
            <div>
                <h2 class="text-xl font-bold text-slate-800 mb-3">Sobre o treinamento</h2>
                @if ($treinamento->descricao)
                    <div class="prose prose-slate max-w-none text-slate-600 whitespace-pre-line">{{ $treinamento->descricao }}</div>
                @else
                    <p class="text-slate-400">Descrição não informada.</p>
                @endif
            </div>

            @if ($treinamento->publico_alvo)
                <div class="rounded-xl border border-slate-200 bg-white p-5">
                    <h3 class="font-semibold text-slate-800 mb-1">Público-alvo</h3>
                    <p class="text-slate-600">{{ $treinamento->publico_alvo }}</p>
                </div>
            @endif
        </div>

        {{-- Barra lateral: inscrição --}}
        <aside class="space-y-5">
            <div id="inscricao" class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden sticky top-6">
                <div class="bg-brand-50 px-5 py-4 border-b border-brand-100">
                    <h3 class="font-semibold text-brand-900">Inscrição</h3>
                </div>
                <div class="p-5 space-y-4">
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500">Vagas</dt>
                            <dd class="font-medium text-slate-800">
                                @if (is_null($treinamento->vagas))
                                    Ilimitadas
                                @else
                                    {{ $treinamento->vagasRestantes() }} de {{ $treinamento->vagas }} disponíveis
                                @endif
                            </dd>
                        </div>
                        @if ($treinamento->inscricoes_ate)
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-500">Inscrições até</dt>
                                <dd class="font-medium text-slate-800">{{ $treinamento->inscricoes_ate->translatedFormat('d/m/Y') }}</dd>
                            </div>
                        @endif
                    </dl>

                    @if (session('inscricao_sucesso'))
                        {{-- Confirmação --}}
                        <div class="rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800">
                            <div class="flex items-center gap-2 font-semibold mb-1">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                Inscrição confirmada!
                            </div>
                            <p>Sua inscrição foi registrada com sucesso.</p>
                            <p class="mt-1">Protocolo: <strong>#{{ str_pad(session('inscricao_sucesso'), 5, '0', STR_PAD_LEFT) }}</strong></p>
                        </div>
                    @elseif ($treinamento->inscricoesAbertas())
                        @if (session('erro'))
                            <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">{{ session('erro') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                                Verifique os campos destacados abaixo.
                            </div>
                        @endif

                        @php
                            $campo = 'w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none';
                        @endphp
                        <form method="POST" action="{{ route('treinamentos.inscrever', $treinamento) }}" class="space-y-3">
                            @csrf
                            <div>
                                <label for="nome" class="block text-xs font-medium text-slate-600 mb-1">Nome completo <span class="text-red-500">*</span></label>
                                <input id="nome" name="nome" value="{{ old('nome') }}" required class="{{ $campo }} @error('nome') border-red-400 @enderror">
                                @error('nome') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="email" class="block text-xs font-medium text-slate-600 mb-1">E-mail <span class="text-red-500">*</span></label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required class="{{ $campo }} @error('email') border-red-400 @enderror">
                                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="cpf" class="block text-xs font-medium text-slate-600 mb-1">CPF</label>
                                    <input id="cpf" name="cpf" value="{{ old('cpf') }}" class="{{ $campo }}" placeholder="000.000.000-00">
                                    @error('cpf') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="telefone" class="block text-xs font-medium text-slate-600 mb-1">Telefone</label>
                                    <input id="telefone" name="telefone" value="{{ old('telefone') }}" class="{{ $campo }}" placeholder="(00) 00000-0000">
                                    @error('telefone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div>
                                <label for="orgao" class="block text-xs font-medium text-slate-600 mb-1">Órgão / Setor</label>
                                <input id="orgao" name="orgao" value="{{ old('orgao') }}" class="{{ $campo }}">
                                @error('orgao') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="cargo" class="block text-xs font-medium text-slate-600 mb-1">Cargo / Função</label>
                                <input id="cargo" name="cargo" value="{{ old('cargo') }}" class="{{ $campo }}">
                                @error('cargo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <button type="submit" class="w-full rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition-colors">
                                Confirmar inscrição
                            </button>
                        </form>
                    @else
                        <div class="rounded-lg bg-slate-50 border border-slate-200 px-4 py-3 text-sm text-slate-600">
                            <div class="flex items-center gap-2 font-medium">
                                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                @if (! is_null($treinamento->vagasRestantes()) && $treinamento->vagasRestantes() === 0)
                                    Vagas esgotadas
                                @else
                                    Inscrições indisponíveis
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <a href="{{ route('agenda') }}" class="flex items-center justify-center gap-1.5 text-sm text-slate-500 hover:text-brand-700">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                Voltar à agenda
            </a>
        </aside>
    </section>
@endsection
