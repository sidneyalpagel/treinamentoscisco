@extends('layouts.admin')

@section('titulo', 'Treinamentos')

@section('conteudo')
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Treinamentos</h1>
            <p class="text-sm text-slate-500">Cadastre e gerencie os treinamentos da agenda.</p>
        </div>
        <a href="{{ route('admin.treinamentos.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Novo treinamento
        </a>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="mb-4 flex flex-wrap items-end gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex-1 min-w-52">
            <label for="busca" class="block text-xs font-medium text-slate-600 mb-1">Buscar</label>
            <input id="busca" name="busca" value="{{ $filtros['busca'] ?? '' }}" placeholder="Título ou instrutor"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none">
        </div>
        <div class="w-48">
            <label for="status" class="block text-xs font-medium text-slate-600 mb-1">Status</label>
            <select id="status" name="status"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none">
                <option value="">Todos</option>
                @foreach ($statusDisponiveis as $valor => $rotulo)
                    <option value="{{ $valor }}" @selected(($filtros['status'] ?? '') === $valor)>{{ $rotulo }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-900 transition-colors">Filtrar</button>
        @if (!empty($filtros['busca']) || !empty($filtros['status']))
            <a href="{{ route('admin.treinamentos.index') }}" class="rounded-lg px-3 py-2 text-sm text-slate-500 hover:text-slate-700">Limpar</a>
        @endif
    </form>

    {{-- Tabela --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Treinamento</th>
                        <th class="px-5 py-3">Início</th>
                        <th class="px-5 py-3">Modalidade</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($treinamentos as $t)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3.5">
                                <a href="{{ route('admin.treinamentos.show', $t) }}" class="font-medium text-slate-800 hover:text-brand-700">{{ $t->titulo }}</a>
                                @if ($t->instrutor)
                                    <div class="text-xs text-slate-500">{{ $t->instrutor }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-slate-600 whitespace-nowrap">{{ $t->data_inicio->translatedFormat('d/m/Y H:i') }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $t->modalidade_label }}</td>
                            <td class="px-5 py-3.5"><x-status-badge :status="$t->status" :label="$t->status_label" /></td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.treinamentos.edit', $t) }}" title="Editar"
                                       class="grid place-items-center w-8 h-8 rounded-md text-slate-500 hover:bg-brand-50 hover:text-brand-700">
                                        <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.treinamentos.destroy', $t) }}"
                                          onsubmit="return confirm('Remover o treinamento “{{ addslashes($t->titulo) }}”? Esta ação não pode ser desfeita.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Remover"
                                                class="grid place-items-center w-8 h-8 rounded-md text-slate-500 hover:bg-red-50 hover:text-red-600">
                                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <div class="text-slate-400">
                                    <svg class="w-10 h-10 mx-auto mb-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
                                    Nenhum treinamento encontrado.
                                    <a href="{{ route('admin.treinamentos.create') }}" class="text-brand-700 hover:underline">Cadastrar o primeiro</a>.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $treinamentos->links() }}
    </div>
@endsection
