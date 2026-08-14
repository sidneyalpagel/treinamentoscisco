<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Plataforma de Treinamentos')</title>
    <meta name="description" content="Agenda e inscrições de treinamentos.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col">

    {{-- Barra superior --}}
    <div class="bg-brand-900 text-brand-100 text-sm">
        <div class="mx-auto max-w-7xl px-4 py-2 flex flex-wrap items-center justify-end gap-2">
            <div class="flex items-center gap-4">
                <a href="{{ route('agenda') }}" class="hover:text-white transition-colors">Agenda</a>
                <a href="{{ route('certificados.validar') }}" class="hidden sm:inline hover:text-white transition-colors">Validar certificado</a>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                    Área do Administrador
                </a>
            </div>
        </div>
    </div>

    {{-- Cabeçalho --}}
    <header class="bg-white border-b border-slate-200 shadow-sm">
        <div class="mx-auto max-w-7xl px-4 py-4 grid grid-cols-2 md:grid-cols-3 items-center gap-6">
            {{-- Esquerda: logo CISCOPAR (usa a imagem quando disponível; emblema como fallback) --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3 justify-self-start">
                @if (file_exists(public_path('images/ciscopar-logo.png')))
                    <img src="{{ asset('images/ciscopar-logo.png') }}" alt="CISCOPAR" class="h-11 w-auto">
                @else
                    <span class="grid place-items-center w-11 h-11 rounded-lg bg-brand-800 text-white">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/></svg>
                    </span>
                    <span class="leading-tight md:hidden">
                        <span class="block text-base font-bold text-brand-800 tracking-tight">Plataforma de Treinamentos</span>
                    </span>
                @endif
            </a>

            {{-- Centro: título da plataforma --}}
            <a href="{{ route('home') }}" class="hidden md:block text-center leading-tight justify-self-center">
                <span class="block text-lg font-bold text-brand-800 tracking-tight">Plataforma de Treinamentos</span>
                <span class="block text-xs text-slate-500">Capacitação e desenvolvimento</span>
            </a>

            {{-- Direita: navegação --}}
            <nav class="hidden md:flex items-center justify-end gap-1 justify-self-end">
                @php($navItens = [['home', 'Início'], ['agenda', 'Agenda']])
                @foreach ($navItens as [$rota, $rotulo])
                    <a href="{{ route($rota) }}"
                       @class([
                           'px-4 py-2 rounded-md text-sm font-medium transition-colors',
                           'bg-brand-50 text-brand-800' => request()->routeIs($rota),
                           'text-slate-600 hover:text-brand-800 hover:bg-slate-50' => ! request()->routeIs($rota),
                       ])>
                        {{ $rotulo }}
                    </a>
                @endforeach
                <a href="{{ route('agenda') }}" class="ml-2 inline-flex items-center gap-2 rounded-md bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800 transition-colors">
                    Ver treinamentos
                </a>
            </nav>
        </div>
    </header>

    {{-- Conteúdo --}}
    <main class="flex-1">
        @yield('conteudo')
    </main>

    {{-- Rodapé --}}
    <footer class="mt-16 bg-brand-900 text-brand-100">
        <div class="mx-auto max-w-7xl px-4 py-10 grid gap-8 md:grid-cols-2">
            <div>
                <div class="flex items-center gap-2 text-white font-semibold text-lg">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342"/></svg>
                    Plataforma de Treinamentos
                </div>
                <p class="mt-3 text-sm text-brand-200/90 max-w-xs">
                    Gestão da agenda de treinamentos, inscrições, listas de presença e certificados.
                </p>
            </div>
            <div>
                <h3 class="text-white font-semibold mb-3">Navegação</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Início</a></li>
                    <li><a href="{{ route('agenda') }}" class="hover:text-white transition-colors">Agenda de treinamentos</a></li>
                    <li><a href="{{ route('certificados.validar') }}" class="hover:text-white transition-colors">Validar certificado</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Área do administrador</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10">
            <div class="mx-auto max-w-7xl px-4 py-4 text-xs text-brand-200/80">
                © {{ date('Y') }} Plataforma de Treinamentos. Todos os direitos reservados.
            </div>
        </div>
    </footer>

</body>
</html>
