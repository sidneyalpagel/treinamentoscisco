<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Painel') · Administração Geral</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100">
<div class="min-h-screen lg:flex">

    {{-- Sidebar --}}
    <aside class="hidden lg:flex lg:flex-col w-64 shrink-0 bg-slate-900 text-slate-300">
        <div class="h-16 flex items-center gap-2.5 px-5 border-b border-white/10">
            <span class="grid place-items-center w-9 h-9 rounded-lg bg-brand-600 text-white">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.623 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
            </span>
            <div class="leading-tight">
                <div class="text-sm font-bold text-white">Administração Geral</div>
                <div class="text-[11px] text-slate-400">Gestão da plataforma</div>
            </div>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1">
            @php
                $itens = [
                    ['rota' => 'gestao.dashboard', 'ativo' => 'gestao.dashboard', 'rotulo' => 'Painel', 'icone' => 'M2.25 12 11.204 3.045c.44-.44 1.152-.44 1.591 0L21.75 12M4.5 9.75v10.5a.75.75 0 0 0 .75.75H9.75v-6a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75v6h4.5a.75.75 0 0 0 .75-.75V9.75'],
                    ['rota' => 'gestao.usuarios.index', 'ativo' => 'gestao.usuarios.*', 'rotulo' => 'Usuários', 'icone' => 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z'],
                    ['rota' => 'gestao.areas.index', 'ativo' => 'gestao.areas.*', 'rotulo' => 'Áreas / Setores', 'icone' => 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z'],
                ];
            @endphp
            @foreach ($itens as $item)
                <a href="{{ route($item['rota']) }}"
                   @class([
                       'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                       'bg-white/10 text-white' => request()->routeIs($item['ativo']),
                       'text-slate-400 hover:bg-white/5 hover:text-white' => ! request()->routeIs($item['ativo']),
                   ])>
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icone'] }}"/></svg>
                    {{ $item['rotulo'] }}
                </a>
            @endforeach
        </nav>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">
        {{-- Topbar --}}
        <header class="bg-white border-b border-slate-200">
            <div class="px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
                <span class="lg:hidden font-bold text-slate-800">Administração Geral</span>
                <div class="flex items-center gap-4 ml-auto">
                    <div class="text-right leading-tight hidden sm:block">
                        <div class="text-sm font-medium text-slate-700">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-slate-400">Administrador Geral</div>
                    </div>
                    <div class="grid place-items-center w-9 h-9 rounded-full bg-slate-800 text-white font-semibold text-sm">
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
            <nav class="lg:hidden flex items-center gap-1 px-4 pb-3 overflow-x-auto">
                @foreach ($itens as $item)
                    <a href="{{ route($item['rota']) }}"
                       @class([
                           'shrink-0 rounded-md px-3 py-1.5 text-sm font-medium transition-colors',
                           'bg-slate-800 text-white' => request()->routeIs($item['ativo']),
                           'text-slate-600 bg-slate-100' => ! request()->routeIs($item['ativo']),
                       ])>{{ $item['rotulo'] }}</a>
                @endforeach
            </nav>
        </header>

        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            <div class="mx-auto max-w-6xl">
                @if (session('sucesso'))
                    <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-start gap-2">
                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        {{ session('sucesso') }}
                    </div>
                @endif
                @if (session('erro'))
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('erro') }}</div>
                @endif
                @yield('conteudo')
            </div>
        </main>
    </div>
</div>
</body>
</html>
