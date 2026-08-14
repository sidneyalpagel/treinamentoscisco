@extends('layouts.admin')

@section('titulo', 'Certificados · '.$treinamento->titulo)

@section('conteudo')
    <div class="mb-6">
        <a href="{{ route('admin.treinamentos.show', $treinamento) }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-brand-700 mb-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            Voltar ao treinamento
        </a>
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Certificados</h1>
                <p class="text-sm text-slate-500">{{ $treinamento->titulo }} · {{ $inscritos->count() }} participante(s) confirmado(s)</p>
            </div>
            @php($pendentes = $inscritos->filter(fn ($i) => ! $i->certificado)->count())
            @if ($pendentes > 0)
                <form method="POST" action="{{ route('admin.certificados.emitir-todos', $treinamento) }}"
                      onsubmit="return confirm('Emitir certificado para os {{ $pendentes }} participante(s) sem certificado?');">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        Emitir todos ({{ $pendentes }})
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if (! $treinamento->gera_certificado)
        <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            A emissão de certificados está <strong>desativada</strong> neste treinamento. Ative em “Editar treinamento”.
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-5 py-3">Participante</th>
                    <th class="px-5 py-3">Certificado</th>
                    <th class="px-5 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($inscritos as $inscrito)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-5 py-3.5">
                            <div class="font-medium text-slate-800">{{ $inscrito->nome }}</div>
                            <div class="text-xs text-slate-500">{{ $inscrito->email }}</div>
                        </td>
                        <td class="px-5 py-3.5">
                            @if ($inscrito->certificado)
                                <span class="inline-flex items-center gap-1.5 text-emerald-700">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                    <span class="font-mono text-xs">{{ $inscrito->certificado->codigo }}</span>
                                </span>
                            @else
                                <span class="text-slate-400 text-xs">Não emitido</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-end gap-2">
                                @if ($inscrito->certificado)
                                    <a href="{{ route('certificados.mostrar', $inscrito->certificado->codigo) }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                        Ver
                                    </a>
                                    <form method="POST" action="{{ route('admin.certificados.destroy', $inscrito->certificado) }}"
                                          onsubmit="return confirm('Revogar o certificado de {{ addslashes($inscrito->nome) }}?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Revogar" class="grid place-items-center w-8 h-8 rounded-md text-slate-500 hover:bg-red-50 hover:text-red-600">
                                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165"/></svg>
                                        </button>
                                    </form>
                                @elseif ($treinamento->gera_certificado)
                                    <form method="POST" action="{{ route('admin.certificados.emitir', $inscrito) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-brand-800 transition-colors">
                                            Emitir
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-12 text-center text-slate-400">
                            Nenhum participante confirmado.
                            <a href="{{ route('admin.inscricoes.index', ['treinamento' => $treinamento->id]) }}" class="text-brand-700 hover:underline">Ver inscrições</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
