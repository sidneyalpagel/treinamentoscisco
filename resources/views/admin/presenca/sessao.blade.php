@extends('layouts.admin')

@section('titulo', 'Lista de presença')

@section('conteudo')
    <div class="mb-6">
        <a href="{{ route('admin.sessoes.index', $sessao->treinamento) }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-brand-700 mb-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            Voltar às sessões
        </a>
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Lista de presença</h1>
                <p class="text-sm text-slate-500">
                    {{ $sessao->treinamento->titulo }} · <strong>{{ $sessao->nome_exibicao }}</strong> · {{ $sessao->data->translatedFormat('d/m/Y') }} {{ $sessao->horario }}
                </p>
            </div>
            <a href="{{ route('admin.sessoes.presenca.exportar', $sessao) }}"
               class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                Exportar CSV
            </a>
        </div>
    </div>

    {{-- Barra de check-in --}}
    <div class="mb-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-6">
            <div>
                <div class="text-2xl font-bold text-slate-800">{{ $presentes->count() }} <span class="text-base font-normal text-slate-400">/ {{ $inscritos->count() }}</span></div>
                <div class="text-xs text-slate-500">presentes</div>
            </div>
            <div class="text-sm">
                <div class="text-xs text-slate-500 mb-0.5">Link de auto check-in</div>
                <div class="font-mono text-xs text-slate-600">{{ route('presenca.form', $sessao->codigo) }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.sessoes.chamada', $sessao) }}">
            @csrf @method('PATCH')
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition-colors {{ $sessao->presenca_aberta ? 'bg-red-50 text-red-700 hover:bg-red-100 ring-1 ring-red-200' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}">
                @if ($sessao->presenca_aberta)
                    <span class="w-2 h-2 rounded-full bg-red-500"></span> Fechar check-in público
                @else
                    <span class="w-2 h-2 rounded-full bg-white"></span> Abrir check-in público
                @endif
            </button>
        </form>
    </div>

    {{-- Tabela de presença --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-5 py-3">Participante</th>
                    <th class="px-5 py-3">Órgão / Setor</th>
                    <th class="px-5 py-3">Registro</th>
                    <th class="px-5 py-3 text-right">Presença</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($inscritos as $inscrito)
                    @php($presente = $presentes->has($inscrito->id))
                    <tr class="{{ $presente ? 'bg-emerald-50/40' : '' }}">
                        <td class="px-5 py-3.5">
                            <div class="font-medium text-slate-800">{{ $inscrito->nome }}</div>
                            <div class="text-xs text-slate-500">{{ $inscrito->email }}</div>
                        </td>
                        <td class="px-5 py-3.5 text-slate-600">{{ $inscrito->orgao ?: '—' }}</td>
                        <td class="px-5 py-3.5 text-slate-500 text-xs">
                            {{ $presente ? \Illuminate\Support\Carbon::parse($presentes[$inscrito->id])->format('d/m/Y H:i') : '—' }}
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <form method="POST" action="{{ route('admin.sessoes.presenca.toggle', [$sessao, $inscrito]) }}" class="inline-flex">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors {{ $presente ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                                    @if ($presente)
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                        Presente
                                    @else
                                        Marcar presença
                                    @endif
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center text-slate-400">
                            Nenhum participante confirmado neste treinamento.
                            <a href="{{ route('admin.inscricoes.index', ['treinamento' => $sessao->treinamento_id]) }}" class="text-brand-700 hover:underline">Ver inscrições</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
