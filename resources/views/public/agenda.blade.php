@extends('layouts.public')

@section('titulo', 'Agenda de treinamentos')

@section('conteudo')
    {{-- Cabeçalho da página --}}
    <section class="bg-brand-800 text-white">
        <div class="mx-auto max-w-7xl px-4 py-12">
            <nav class="text-sm text-brand-200 mb-3">
                <a href="{{ route('home') }}" class="hover:text-white">Início</a>
                <span class="mx-1.5 opacity-60">/</span>
                <span>Agenda</span>
            </nav>
            <h1 class="text-3xl lg:text-4xl font-bold">Agenda de treinamentos</h1>
            <p class="mt-2 text-brand-100/90 max-w-2xl">Confira todos os treinamentos publicados e faça sua inscrição.</p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-12">
        @forelse ($treinamentos as $mes => $lista)
            <div class="mb-10">
                <h2 class="flex items-center gap-3 text-lg font-semibold text-slate-800 mb-5">
                    <span class="grid place-items-center w-9 h-9 rounded-lg bg-brand-50 text-brand-700">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                    </span>
                    <span class="first-letter:uppercase">{{ $mes }}</span>
                </h2>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($lista as $treinamento)
                        <x-treinamento-card :treinamento="$treinamento" />
                    @endforeach
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-slate-300 bg-white py-20 text-center">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                <p class="text-slate-500">Nenhum treinamento publicado na agenda.</p>
            </div>
        @endforelse
    </section>
@endsection
