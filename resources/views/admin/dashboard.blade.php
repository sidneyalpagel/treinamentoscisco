@extends('layouts.admin')

@section('titulo', 'Painel')

@section('conteudo')
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Painel</h1>
            <p class="text-sm text-slate-500">Visão geral dos treinamentos.</p>
        </div>
        <a href="{{ route('admin.treinamentos.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Novo treinamento
        </a>
    </div>

    {{-- Métricas --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @php
            $cards = [
                ['rotulo' => 'Total de treinamentos', 'valor' => $metricas['total'], 'cor' => 'brand'],
                ['rotulo' => 'Publicados', 'valor' => $metricas['publicados'], 'cor' => 'emerald'],
                ['rotulo' => 'Inscrições', 'valor' => $metricas['inscricoes'], 'cor' => 'sky'],
                ['rotulo' => 'Próximos', 'valor' => $metricas['proximos'], 'cor' => 'amber'],
            ];
        @endphp
        @foreach ($cards as $card)
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-sm text-slate-500">{{ $card['rotulo'] }}</div>
                <div class="mt-2 text-3xl font-bold text-slate-800">{{ $card['valor'] }}</div>
            </div>
        @endforeach
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        {{-- Próximos treinamentos --}}
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="font-semibold text-slate-800">Próximos treinamentos</h2>
                <a href="{{ route('admin.treinamentos.index') }}" class="text-sm text-brand-700 hover:underline">Ver todos</a>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse ($proximosTreinamentos as $t)
                    <li class="px-5 py-3.5 flex items-center justify-between gap-4">
                        <div class="min-w-0">
                            <a href="{{ route('admin.treinamentos.show', $t) }}" class="font-medium text-slate-800 hover:text-brand-700 truncate block">{{ $t->titulo }}</a>
                            <div class="text-xs text-slate-500">{{ $t->data_inicio->translatedFormat('d \d\e F \d\e Y, H:i') }}</div>
                        </div>
                        <x-status-badge :status="$t->status" :label="$t->status_label" />
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-sm text-slate-400">Nenhum treinamento agendado.</li>
                @endforelse
            </ul>
        </div>

        {{-- Cadastrados recentemente --}}
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="font-semibold text-slate-800">Cadastrados recentemente</h2>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse ($recentes as $t)
                    <li class="px-5 py-3.5 flex items-center justify-between gap-4">
                        <div class="min-w-0">
                            <a href="{{ route('admin.treinamentos.show', $t) }}" class="font-medium text-slate-800 hover:text-brand-700 truncate block">{{ $t->titulo }}</a>
                            <div class="text-xs text-slate-500">Criado em {{ $t->created_at->translatedFormat('d/m/Y') }}</div>
                        </div>
                        <x-status-badge :status="$t->status" :label="$t->status_label" />
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-sm text-slate-400">Nenhum treinamento cadastrado ainda.</li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection
