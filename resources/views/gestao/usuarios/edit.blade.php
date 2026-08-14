@extends('layouts.gestao')

@section('titulo', 'Editar usuário')

@section('conteudo')
    <div class="mb-6">
        <a href="{{ route('gestao.usuarios.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-brand-700 mb-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            Voltar
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Editar usuário</h1>
        <p class="text-sm text-slate-500">{{ $usuario->name }} · {{ $usuario->email }}</p>
    </div>

    <form method="POST" action="{{ route('gestao.usuarios.update', $usuario) }}">
        @csrf @method('PUT')
        @include('gestao.usuarios._form', ['textoBotao' => 'Salvar alterações'])
    </form>

    {{-- Redefinir senha --}}
    <div class="mt-6 rounded-xl border border-amber-200 bg-white p-6 shadow-sm max-w-2xl">
        <h2 class="font-semibold text-slate-800 mb-1">Redefinir senha</h2>
        <p class="text-sm text-slate-500 mb-4">Defina uma nova senha para este usuário. Informe a nova senha ao gestor com segurança.</p>
        @php($campo = 'w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none')
        <form method="POST" action="{{ route('gestao.usuarios.senha', $usuario) }}" class="grid gap-4 sm:grid-cols-2">
            @csrf @method('PATCH')
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Nova senha</label>
                <input id="password" name="password" type="password" required class="{{ $campo }} @error('password') border-red-400 @enderror" placeholder="Mínimo 8 caracteres">
                @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">Confirmar nova senha</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required class="{{ $campo }}">
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-700 transition-colors">Redefinir senha</button>
            </div>
        </form>
    </div>
@endsection
