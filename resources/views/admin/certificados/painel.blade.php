@extends('layouts.admin')

@section('titulo', 'Certificados')

@section('conteudo')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Certificados</h1>
        <p class="text-sm text-slate-500">Treinamentos configurados para emitir certificado.</p>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Treinamento</th>
                        <th class="px-5 py-3">Data</th>
                        <th class="px-5 py-3">Confirmados</th>
                        <th class="px-5 py-3">Emitidos</th>
                        <th class="px-5 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($treinamentos as $t)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3.5 font-medium text-slate-800">{{ $t->titulo }}</td>
                            <td class="px-5 py-3.5 text-slate-600 whitespace-nowrap">{{ $t->data_inicio->format('d/m/Y') }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $t->confirmados_count }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 text-brand-700 ring-1 ring-inset ring-brand-600/20 px-2.5 py-1 text-xs font-medium">
                                    {{ $t->emitidos_count }} / {{ $t->confirmados_count }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('admin.certificados.index', $t) }}" class="text-sm font-medium text-brand-700 hover:underline">Gerenciar</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-slate-400">
                                Nenhum treinamento com emissão de certificado habilitada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $treinamentos->links() }}</div>
@endsection
