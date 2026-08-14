@extends('layouts.gestao')

@section('titulo', 'Áreas / Setores')

@section('conteudo')
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Áreas / Setores</h1>
            <p class="text-sm text-slate-500">Setores da CISCOPAR aos quais os gestores são vinculados.</p>
        </div>
        <a href="{{ route('gestao.areas.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Nova área
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Área</th>
                        <th class="px-5 py-3">Sigla</th>
                        <th class="px-5 py-3">Gestores</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($areas as $area)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3.5">
                                <div class="font-medium text-slate-800">{{ $area->nome }}</div>
                                @if ($area->descricao)
                                    <div class="text-xs text-slate-500 truncate max-w-md">{{ $area->descricao }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $area->sigla ?: '—' }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $area->usuarios_count }}</td>
                            <td class="px-5 py-3.5"><x-status-badge :status="$area->ativo ? 'publicado' : 'encerrado'" :label="$area->ativo ? 'Ativa' : 'Inativa'" /></td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('gestao.areas.edit', $area) }}" title="Editar" class="grid place-items-center w-8 h-8 rounded-md text-slate-500 hover:bg-brand-50 hover:text-brand-700">
                                        <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('gestao.areas.destroy', $area) }}" onsubmit="return confirm('Remover a área “{{ addslashes($area->nome) }}”?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Remover" class="grid place-items-center w-8 h-8 rounded-md text-slate-500 hover:bg-red-50 hover:text-red-600">
                                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-12 text-center text-slate-400">Nenhuma área cadastrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $areas->links() }}</div>
@endsection
