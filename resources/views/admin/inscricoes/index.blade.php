@extends('layouts.admin')

@section('titulo', 'Inscrições')

@section('conteudo')
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Inscrições</h1>
            <p class="text-sm text-slate-500">{{ $total }} {{ $total === 1 ? 'inscrição encontrada' : 'inscrições encontradas' }}.</p>
        </div>
        <a href="{{ route('admin.inscricoes.exportar', $filtros) }}"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
            Exportar CSV
        </a>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="mb-4 flex flex-wrap items-end gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex-1 min-w-52">
            <label for="busca" class="block text-xs font-medium text-slate-600 mb-1">Buscar</label>
            <input id="busca" name="busca" value="{{ $filtros['busca'] ?? '' }}" placeholder="Nome, e-mail ou órgão"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none">
        </div>
        <div class="w-64">
            <label for="treinamento" class="block text-xs font-medium text-slate-600 mb-1">Treinamento</label>
            <select id="treinamento" name="treinamento"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none">
                <option value="">Todos</option>
                @foreach ($treinamentos as $t)
                    <option value="{{ $t->id }}" @selected((string) ($filtros['treinamento'] ?? '') === (string) $t->id)>{{ $t->titulo }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-44">
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
        @if (!empty(array_filter($filtros)))
            <a href="{{ route('admin.inscricoes.index') }}" class="rounded-lg px-3 py-2 text-sm text-slate-500 hover:text-slate-700">Limpar</a>
        @endif
    </form>

    {{-- Tabela --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Participante</th>
                        <th class="px-5 py-3">Treinamento</th>
                        <th class="px-5 py-3">Órgão / Setor</th>
                        <th class="px-5 py-3">Inscrição</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($inscricoes as $i)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3.5">
                                <div class="font-medium text-slate-800">{{ $i->nome }}</div>
                                <div class="text-xs text-slate-500">{{ $i->email }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <a href="{{ route('admin.treinamentos.show', $i->treinamento) }}" class="text-slate-600 hover:text-brand-700">{{ $i->treinamento?->titulo }}</a>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $i->orgao ?: '—' }}</td>
                            <td class="px-5 py-3.5 text-slate-600 whitespace-nowrap">{{ $i->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-3.5">
                                <form method="POST" action="{{ route('admin.inscricoes.update', $i) }}" class="inline-flex">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()"
                                            class="rounded-md border border-slate-300 py-1 pl-2 pr-7 text-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500/30 focus:outline-none">
                                        @foreach ($statusDisponiveis as $valor => $rotulo)
                                            <option value="{{ $valor }}" @selected($i->status === $valor)>{{ $rotulo }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end">
                                    <form method="POST" action="{{ route('admin.inscricoes.destroy', $i) }}"
                                          onsubmit="return confirm('Remover a inscrição de {{ addslashes($i->nome) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Remover"
                                                class="grid place-items-center w-8 h-8 rounded-md text-slate-500 hover:bg-red-50 hover:text-red-600">
                                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <div class="text-slate-400">
                                    <svg class="w-10 h-10 mx-auto mb-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                                    Nenhuma inscrição encontrada.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $inscricoes->links() }}
    </div>
@endsection
