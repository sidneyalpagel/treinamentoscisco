<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Painel') · Administração</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100">
<div class="min-h-screen lg:flex">

    {{-- Sidebar --}}
    <aside class="hidden lg:flex lg:flex-col w-64 shrink-0 bg-brand-900 text-brand-100">
        @include('admin.partials.sidebar')
    </aside>

    {{-- Área principal --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Topbar --}}
        <header class="bg-white border-b border-slate-200">
            <div class="px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" target="_blank" class="text-sm text-slate-500 hover:text-brand-700 inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                        Ver site público
                    </a>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right leading-tight hidden sm:block">
                        <div class="text-sm font-medium text-slate-700">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-slate-400">Administrador</div>
                    </div>
                    <div class="grid place-items-center w-9 h-9 rounded-full bg-brand-100 text-brand-800 font-semibold text-sm">
                        {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-slate-500 hover:text-red-600 inline-flex items-center gap-1.5" title="Sair">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/></svg>
                            <span class="hidden sm:inline">Sair</span>
                        </button>
                    </form>
                </div>
            </div>
            {{-- Navegação mobile --}}
            <nav class="lg:hidden flex items-center gap-1 px-4 pb-3 overflow-x-auto">
                @include('admin.partials.nav-mobile')
            </nav>
        </header>

        {{-- Conteúdo --}}
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            <div class="mx-auto max-w-6xl">

                @if (session('sucesso'))
                    <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-start gap-2">
                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        {{ session('sucesso') }}
                    </div>
                @endif

                @if (session('erro'))
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        {{ session('erro') }}
                    </div>
                @endif

                @yield('conteudo')
            </div>
        </main>
    </div>
</div>
</body>
</html>
