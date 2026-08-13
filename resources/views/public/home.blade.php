@extends('layouts.public')

@section('titulo', 'Plataforma de Treinamentos')

@section('conteudo')
    {{-- Hero --}}
    <section class="relative bg-brand-900 text-white overflow-hidden">
        <div class="absolute inset-0 hero-overlay"></div>
        <div class="absolute inset-0 opacity-10"
             style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 28px 28px;"></div>
        <div class="relative mx-auto max-w-7xl px-4 py-20 lg:py-28">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-sm text-brand-100 ring-1 ring-white/15">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    Agenda de capacitação
                </span>
                <h1 class="mt-5 text-4xl lg:text-5xl font-bold leading-tight">
                    Treinamentos e desenvolvimento profissional
                </h1>
                <p class="mt-5 text-lg text-brand-100/90">
                    Consulte a agenda, inscreva-se nos treinamentos disponíveis e acompanhe sua participação —
                    tudo em um só lugar.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('agenda') }}"
                       class="inline-flex items-center gap-2 rounded-lg bg-white px-5 py-3 text-sm font-semibold text-brand-800 shadow-sm hover:bg-brand-50 transition-colors">
                        Ver agenda completa
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </a>
                    <a href="#proximos"
                       class="inline-flex items-center gap-2 rounded-lg bg-white/10 px-5 py-3 text-sm font-semibold text-white ring-1 ring-white/20 hover:bg-white/15 transition-colors">
                        Próximos treinamentos
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Próximos treinamentos --}}
    <section id="proximos" class="mx-auto max-w-7xl px-4 py-16">
        <div class="flex items-end justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Próximos treinamentos</h2>
                <p class="text-slate-500 mt-1">Treinamentos publicados com inscrições disponíveis.</p>
            </div>
            <a href="{{ route('agenda') }}" class="hidden sm:inline-flex items-center gap-1.5 text-sm font-medium text-brand-700 hover:underline">
                Ver todos
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
            </a>
        </div>

        @if ($proximos->isEmpty())
            <div class="rounded-xl border border-dashed border-slate-300 bg-white py-16 text-center">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                <p class="text-slate-500">Nenhum treinamento disponível no momento.</p>
                <p class="text-sm text-slate-400 mt-1">Volte em breve para conferir a agenda.</p>
            </div>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($proximos as $treinamento)
                    <x-treinamento-card :treinamento="$treinamento" />
                @endforeach
            </div>
        @endif
    </section>
@endsection
