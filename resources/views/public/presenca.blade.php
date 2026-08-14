<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro de presença · {{ $sessao->treinamento->titulo }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 flex flex-col">
    <div class="bg-brand-900 text-white">
        <div class="mx-auto max-w-lg px-4 py-4 flex items-center gap-2.5">
            <span class="grid place-items-center w-9 h-9 rounded-lg bg-white/10">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814"/></svg>
            </span>
            <span class="font-semibold">Plataforma de Treinamentos</span>
        </div>
    </div>

    <main class="flex-1 flex items-start justify-center p-4">
        <div class="w-full max-w-lg mt-6">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="bg-brand-50 border-b border-brand-100 px-6 py-5">
                    <div class="text-xs font-semibold uppercase tracking-wider text-brand-600">Registro de presença</div>
                    <h1 class="mt-1 text-xl font-bold text-brand-900">{{ $sessao->treinamento->titulo }}</h1>
                    <p class="mt-1 text-sm text-slate-600">
                        {{ $sessao->nome_exibicao }} · {{ $sessao->data->translatedFormat('d/m/Y') }} · {{ $sessao->horario }}
                    </p>
                </div>

                <div class="p-6">
                    @if (session('presenca_ok'))
                        {{-- Confirmação --}}
                        <div class="text-center py-4">
                            <div class="mx-auto grid place-items-center w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 mb-4">
                                <svg class="w-9 h-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                            </div>
                            <h2 class="text-lg font-bold text-slate-800">
                                {{ session('presenca_repetida') ? 'Presença já registrada' : 'Presença confirmada!' }}
                            </h2>
                            <p class="mt-1 text-slate-600">{{ session('presenca_ok') }}</p>
                            <p class="mt-1 text-sm text-slate-400">Sua presença nesta sessão está registrada.</p>
                        </div>
                    @elseif (! $sessao->presenca_aberta)
                        {{-- Fechado --}}
                        <div class="text-center py-4">
                            <div class="mx-auto grid place-items-center w-16 h-16 rounded-full bg-slate-100 text-slate-400 mb-4">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                            </div>
                            <h2 class="text-lg font-bold text-slate-800">Check-in fechado</h2>
                            <p class="mt-1 text-slate-500">O registro de presença desta sessão não está aberto no momento. Aguarde a liberação pelo responsável.</p>
                        </div>
                    @else
                        {{-- Formulário --}}
                        @if (session('erro'))
                            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">{{ session('erro') }}</div>
                        @endif
                        <p class="text-sm text-slate-600 mb-4">Informe o e-mail utilizado na sua inscrição para registrar presença.</p>
                        <form method="POST" action="{{ route('presenca.registrar', $sessao->codigo) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">E-mail da inscrição</label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                                       class="w-full rounded-lg border border-slate-300 px-3.5 py-3 text-base focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none @error('email') border-red-400 @enderror"
                                       placeholder="voce@exemplo.gov.br">
                                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <button type="submit" class="w-full rounded-lg bg-brand-700 px-4 py-3 text-base font-semibold text-white shadow-sm hover:bg-brand-800 transition-colors">
                                Registrar presença
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            <p class="text-center text-xs text-slate-400 mt-6">© {{ date('Y') }} Plataforma de Treinamentos</p>
        </div>
    </main>
</body>
</html>
