<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ativar cadastro · Plataforma de Treinamentos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen grid lg:grid-cols-2">

    {{-- Coluna institucional --}}
    <div class="hidden lg:flex relative hero-overlay text-white p-12 flex-col justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <span class="grid place-items-center w-11 h-11 rounded-lg bg-white/10">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342"/></svg>
            </span>
            <span class="text-lg font-bold">Plataforma de Treinamentos</span>
        </a>
        <div>
            <h1 class="text-3xl font-bold leading-tight">Bem-vindo(a) à<br>plataforma</h1>
            <p class="mt-4 text-brand-100/90 max-w-md">Defina sua senha para concluir o cadastro e acessar o painel de gestão.</p>
        </div>
        <p class="text-sm text-brand-200/80">© {{ date('Y') }} Plataforma de Treinamentos</p>
    </div>

    {{-- Coluna do formulário --}}
    <div class="flex items-center justify-center p-6 sm:p-12 bg-slate-50">
        <div class="w-full max-w-sm">
            <div class="lg:hidden mb-8 flex items-center gap-3">
                <span class="grid place-items-center w-11 h-11 rounded-lg bg-brand-800 text-white">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347"/></svg>
                </span>
                <span class="text-lg font-bold text-brand-800">Plataforma de Treinamentos</span>
            </div>

            @if ($invalido || $expirado)
                <h2 class="text-2xl font-bold text-slate-800">Link {{ $expirado ? 'expirado' : 'inválido' }}</h2>
                <p class="mt-2 text-sm text-slate-500">
                    @if ($expirado)
                        Este convite venceu. Peça ao administrador para reenviar o convite de acesso.
                    @else
                        Este link de ativação não é válido ou já foi utilizado. Se você já definiu a senha, faça login normalmente.
                    @endif
                </p>
                <a href="{{ route('login') }}" class="mt-6 inline-flex items-center justify-center w-full rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-800 transition-colors">Ir para o login</a>
            @else
                <h2 class="text-2xl font-bold text-slate-800">Confirmar cadastro</h2>
                <p class="mt-1 text-sm text-slate-500">Olá, <strong>{{ $usuario->name }}</strong>. Defina uma senha para acessar.</p>

                @if ($errors->any())
                    <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('ativar.store', $token) }}" class="mt-6 space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">E-mail</label>
                        <input type="email" value="{{ $usuario->email }}" disabled
                               class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3.5 py-2.5 text-sm text-slate-500">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Nova senha</label>
                        <input id="password" name="password" type="password" required autofocus autocomplete="new-password" minlength="8"
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none"
                               placeholder="Mínimo 8 caracteres">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">Confirmar senha</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none"
                               placeholder="••••••••">
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition-colors">Confirmar e acessar</button>
                </form>
            @endif
        </div>
    </div>

</body>
</html>
