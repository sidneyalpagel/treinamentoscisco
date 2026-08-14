@extends('layouts.gestao')

@section('titulo', 'Painel')

@section('conteudo')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Administração Geral</h1>
        <p class="text-sm text-slate-500">Gestão de usuários e áreas da plataforma.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @php
            $cards = [
                ['rotulo' => 'Gestores', 'valor' => $metricas['gestores']],
                ['rotulo' => 'Gestores ativos', 'valor' => $metricas['gestores_ativos']],
                ['rotulo' => 'Áreas / Setores', 'valor' => $metricas['areas']],
                ['rotulo' => 'Treinamentos', 'valor' => $metricas['treinamentos']],
            ];
        @endphp
        @foreach ($cards as $card)
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-sm text-slate-500">{{ $card['rotulo'] }}</div>
                <div class="mt-2 text-3xl font-bold text-slate-800">{{ $card['valor'] }}</div>
            </div>
        @endforeach
    </div>

    <div class="mt-8 flex flex-wrap gap-3">
        <a href="{{ route('gestao.usuarios.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Novo usuário
        </a>
        <a href="{{ route('gestao.areas.create') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
            Nova área
        </a>
    </div>

    <div class="mt-8 rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-semibold text-slate-800">Gestores recentes</h2>
            <a href="{{ route('gestao.usuarios.index') }}" class="text-sm text-brand-700 hover:underline">Ver todos</a>
        </div>
        <ul class="divide-y divide-slate-100">
            @forelse ($gestoresRecentes as $gestor)
                <li class="px-5 py-3.5 flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <div class="font-medium text-slate-800">{{ $gestor->name }}</div>
                        <div class="text-xs text-slate-500">{{ $gestor->email }} · {{ optional($gestor->area)->nome ?? 'Sem área' }}</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-slate-500">{{ $gestor->treinamentos_count }} treinamento(s)</span>
                        <x-status-badge :status="$gestor->ativo ? 'publicado' : 'encerrado'" :label="$gestor->ativo ? 'Ativo' : 'Inativo'" />
                    </div>
                </li>
            @empty
                <li class="px-5 py-8 text-center text-sm text-slate-400">Nenhum gestor cadastrado ainda.</li>
            @endforelse
        </ul>
    </div>
@endsection
