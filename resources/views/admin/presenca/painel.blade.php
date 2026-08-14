@extends('layouts.admin')

@section('titulo', 'Presença')

@section('conteudo')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Presença</h1>
        <p class="text-sm text-slate-500">Todas as sessões cadastradas. As sessões são criadas dentro de cada treinamento.</p>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Sessão</th>
                        <th class="px-5 py-3">Treinamento</th>
                        <th class="px-5 py-3">Data / Horário</th>
                        <th class="px-5 py-3">Presentes</th>
                        <th class="px-5 py-3">Check-in</th>
                        <th class="px-5 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($sessoes as $sessao)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3.5 font-medium text-slate-800">{{ $sessao->nome_exibicao }}</td>
                            <td class="px-5 py-3.5">
                                <a href="{{ route('admin.treinamentos.show', $sessao->treinamento) }}" class="text-slate-600 hover:text-brand-700">{{ $sessao->treinamento?->titulo }}</a>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600 whitespace-nowrap">{{ $sessao->data->translatedFormat('d/m/Y') }} · {{ $sessao->horario }}</td>
                            <td class="px-5 py-3.5"><span class="font-semibold text-brand-700">{{ $sessao->presencas_count }}</span></td>
                            <td class="px-5 py-3.5">
                                @if ($sessao->presenca_aberta)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aberto</span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs text-slate-400"><span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> Fechado</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('admin.sessoes.presenca', $sessao) }}" class="text-sm font-medium text-brand-700 hover:underline">Lista de presença</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                                Nenhuma sessão cadastrada. Abra um treinamento e adicione sessões em “Sessões e presença”.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $sessoes->links() }}</div>
@endsection
