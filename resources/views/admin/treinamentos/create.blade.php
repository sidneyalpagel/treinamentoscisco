@extends('layouts.admin')

@section('titulo', 'Novo treinamento')

@section('conteudo')
    <div class="mb-6">
        <a href="{{ route('admin.treinamentos.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-brand-700 mb-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            Voltar para a lista
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Novo treinamento</h1>
        <p class="text-sm text-slate-500">Preencha os dados para cadastrar um treinamento na agenda.</p>
    </div>

    <form method="POST" action="{{ route('admin.treinamentos.store') }}">
        @csrf
        @include('admin.treinamentos._form', ['textoBotao' => 'Cadastrar treinamento'])
    </form>
@endsection
