@extends('layouts.gestao')

@section('titulo', 'Novo usuário')

@section('conteudo')
    <div class="mb-6">
        <a href="{{ route('gestao.usuarios.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-brand-700 mb-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            Voltar
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Novo usuário</h1>
        <p class="text-sm text-slate-500">Cadastre um gestor de treinamentos ou outro administrador.</p>
    </div>

    <div class="mb-5 flex items-start gap-2.5 rounded-lg border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-800 max-w-2xl">
        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
        <span>O usuário receberá um <strong>e-mail de convite</strong> para confirmar o cadastro e definir a própria senha. Configure o SMTP em <a href="{{ route('gestao.configuracoes.edit') }}" class="underline">Configurações</a> antes.</span>
    </div>

    <form method="POST" action="{{ route('gestao.usuarios.store') }}">
        @csrf
        @include('gestao.usuarios._form', ['textoBotao' => 'Cadastrar e enviar convite'])
    </form>
@endsection
