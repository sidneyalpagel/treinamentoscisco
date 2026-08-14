@extends('layouts.gestao')

@section('titulo', 'Usuários')

@section('conteudo')
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Usuários</h1>
            <p class="text-sm text-slate-500">Gestores de treinamentos e administradores da plataforma.</p>
        </div>
        <a href="{{ route('gestao.usuarios.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Novo usuário
        </a>
    </div>

    <form method="GET" class="mb-4 flex flex-wrap items-end gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex-1 min-w-52">
            <label for="busca" class="block text-xs font-medium text-slate-600 mb-1">Buscar</label>
            <input id="busca" name="busca" value="{{ $filtros['busca'] ?? '' }}" placeholder="Nome ou e-mail" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none">
        </div>
        <div class="w-56">
            <label for="role" class="block text-xs font-medium text-slate-600 mb-1">Papel</label>
            <select id="role" name="role" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none">
                <option value="">Todos</option>
                @foreach ($rolesDisponiveis as $valor => $rotulo)
                    <option value="{{ $valor }}" @selected(($filtros['role'] ?? '') === $valor)>{{ $rotulo }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-900 transition-colors">Filtrar</button>
        @if (!empty(array_filter($filtros)))
            <a href="{{ route('gestao.usuarios.index') }}" class="rounded-lg px-3 py-2 text-sm text-slate-500 hover:text-slate-700">Limpar</a>
        @endif
    </form>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Usuário</th>
                        <th class="px-5 py-3">Papel</th>
                        <th class="px-5 py-3">Área</th>
                        <th class="px-5 py-3">Treinamentos</th>
                        <th class="px-5 py-3">Acesso</th>
                        <th class="px-5 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($usuarios as $u)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3.5">
                                <div class="font-medium text-slate-800">{{ $u->name }}</div>
                                <div class="text-xs text-slate-500">{{ $u->email }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span @class([
                                    'inline-flex rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset',
                                    'bg-brand-50 text-brand-700 ring-brand-600/20' => $u->isAdmin(),
                                    'bg-slate-100 text-slate-600 ring-slate-500/20' => ! $u->isAdmin(),
                                ])>{{ $u->role_label }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">{{ optional($u->area)->nome ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $u->treinamentos_count }}</td>
                            <td class="px-5 py-3.5">
                                @if ($u->pendenteAtivacao())
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pendente
                                    </span>
                                @elseif ($u->ativo)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Ativo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inativo
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('gestao.usuarios.edit', $u) }}" title="Editar" class="grid place-items-center w-8 h-8 rounded-md text-slate-500 hover:bg-brand-50 hover:text-brand-700">
                                        <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                                    </a>
                                    @if ($u->pendenteAtivacao())
                                        <form method="POST" action="{{ route('gestao.usuarios.convite', $u) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" title="Reenviar convite" class="grid place-items-center w-8 h-8 rounded-md text-slate-500 hover:bg-brand-50 hover:text-brand-700">
                                                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                    @if ($u->id !== auth()->id())
                                        <form method="POST" action="{{ route('gestao.usuarios.status', $u) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" title="{{ $u->ativo ? 'Desativar' : 'Ativar' }}" class="grid place-items-center w-8 h-8 rounded-md text-slate-500 hover:bg-slate-100">
                                                @if ($u->ativo)
                                                    <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                @else
                                                    <svg class="w-4.5 h-4.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                                @endif
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('gestao.usuarios.destroy', $u) }}" onsubmit="return confirm('Remover o usuário {{ addslashes($u->name) }}?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Remover" class="grid place-items-center w-8 h-8 rounded-md text-slate-500 hover:bg-red-50 hover:text-red-600">
                                                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165"/></svg>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-400 px-2">Você</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-12 text-center text-slate-400">Nenhum usuário encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $usuarios->links() }}</div>
@endsection
