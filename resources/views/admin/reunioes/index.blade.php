@extends('layouts.admin')

@section('titulo', 'Reuniões')

@section('conteudo')
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Reuniões</h1>
            <p class="text-sm text-slate-500">Reuniões online avulsas, com sala e gravação.</p>
        </div>
        <a href="{{ route('admin.reunioes.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Nova reunião
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Reunião</th>
                        <th class="px-5 py-3">Início</th>
                        <th class="px-5 py-3">Gravações</th>
                        <th class="px-5 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($reunioes as $r)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3.5">
                                <a href="{{ route('admin.reunioes.show', $r) }}" class="font-medium text-slate-800 hover:text-brand-700">{{ $r->titulo }}</a>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $r->data_inicio->translatedFormat('d/m/Y H:i') }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $r->gravacoes_count }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.reunioes.entrar', $r) }}" target="_blank" title="Entrar na sala" class="grid place-items-center w-8 h-8 rounded-md text-brand-700 hover:bg-brand-50">
                                        <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.reunioes.show', $r) }}" title="Detalhes" class="grid place-items-center w-8 h-8 rounded-md text-slate-500 hover:bg-slate-100">
                                        <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.reunioes.destroy', $r) }}" onsubmit="return confirm('Remover a reunião “{{ addslashes($r->titulo) }}”?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Remover" class="grid place-items-center w-8 h-8 rounded-md text-slate-500 hover:bg-red-50 hover:text-red-600">
                                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-12 text-center text-slate-400">Nenhuma reunião cadastrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $reunioes->links() }}</div>
@endsection
