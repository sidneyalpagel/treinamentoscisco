@extends('layouts.public')

@section('titulo', 'Validar certificado')

@section('conteudo')
    <section class="bg-brand-800 text-white">
        <div class="mx-auto max-w-7xl px-4 py-12">
            <nav class="text-sm text-brand-200 mb-3">
                <a href="{{ route('home') }}" class="hover:text-white">Início</a>
                <span class="mx-1.5 opacity-60">/</span>
                <span>Validar certificado</span>
            </nav>
            <h1 class="text-3xl lg:text-4xl font-bold">Validação de certificado</h1>
            <p class="mt-2 text-brand-100/90 max-w-2xl">Informe o código impresso no certificado para verificar sua autenticidade.</p>
        </div>
    </section>

    <section class="mx-auto max-w-2xl px-4 py-12">
        <form method="GET" action="{{ route('certificados.validar') }}" class="flex flex-col sm:flex-row gap-3">
            <input name="codigo" value="{{ $codigo }}" required placeholder="Ex.: ABCD-2345-EFGH"
                   class="flex-1 rounded-lg border border-slate-300 px-4 py-3 text-sm font-mono uppercase focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none">
            <button type="submit" class="rounded-lg bg-brand-700 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition-colors">
                Validar
            </button>
        </form>

        @if ($buscou)
            <div class="mt-8">
                @if ($certificado)
                    <div class="rounded-xl border border-emerald-200 bg-white shadow-sm overflow-hidden">
                        <div class="bg-emerald-50 border-b border-emerald-100 px-6 py-4 flex items-center gap-2 text-emerald-800">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            <span class="font-semibold">Certificado válido</span>
                        </div>
                        <div class="p-6">
                            <dl class="space-y-3 text-sm">
                                <div class="flex justify-between gap-4">
                                    <dt class="text-slate-500">Participante</dt>
                                    <dd class="font-medium text-slate-800 text-right">{{ $certificado->inscricao->nome }}</dd>
                                </div>
                                <div class="flex justify-between gap-4">
                                    <dt class="text-slate-500">Treinamento</dt>
                                    <dd class="font-medium text-slate-800 text-right">{{ $certificado->inscricao->treinamento->titulo }}</dd>
                                </div>
                                @if ($certificado->carga_horaria)
                                    <div class="flex justify-between gap-4">
                                        <dt class="text-slate-500">Carga horária</dt>
                                        <dd class="font-medium text-slate-800">{{ $certificado->carga_horaria }} horas</dd>
                                    </div>
                                @endif
                                <div class="flex justify-between gap-4">
                                    <dt class="text-slate-500">Emitido em</dt>
                                    <dd class="font-medium text-slate-800">{{ $certificado->emitido_em->translatedFormat('d/m/Y') }}</dd>
                                </div>
                                <div class="flex justify-between gap-4">
                                    <dt class="text-slate-500">Código</dt>
                                    <dd class="font-mono font-semibold text-brand-800">{{ $certificado->codigo }}</dd>
                                </div>
                            </dl>
                            <a href="{{ route('certificados.mostrar', $certificado->codigo) }}" target="_blank"
                               class="mt-6 inline-flex items-center gap-2 rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-800 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                Ver certificado
                            </a>
                        </div>
                    </div>
                @else
                    <div class="rounded-xl border border-red-200 bg-white shadow-sm overflow-hidden">
                        <div class="bg-red-50 border-b border-red-100 px-6 py-4 flex items-center gap-2 text-red-800">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                            <span class="font-semibold">Certificado não encontrado</span>
                        </div>
                        <div class="p-6 text-sm text-slate-600">
                            Não localizamos nenhum certificado com o código <span class="font-mono font-semibold">{{ $codigo }}</span>.
                            Verifique se digitou corretamente.
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </section>
@endsection
