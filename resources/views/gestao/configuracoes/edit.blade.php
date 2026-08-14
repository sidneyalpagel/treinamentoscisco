@extends('layouts.gestao')

@section('titulo', 'Configurações de e-mail')

@section('conteudo')
    @php($campo = 'w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none')

    <div class="mb-6 flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Configurações de e-mail (SMTP)</h1>
            <p class="text-sm text-slate-500">Servidor de envio usado para convites de gestores e links de gravação.</p>
        </div>
        @if ($configurado)
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Configurado
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Não configurado
            </span>
        @endif
    </div>

    {{-- Formulário principal --}}
    <form method="POST" action="{{ route('gestao.configuracoes.update') }}">
        @csrf @method('PUT')
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm space-y-5 max-w-2xl">
            <div class="grid gap-5 sm:grid-cols-3">
                <div class="sm:col-span-2">
                    <label for="smtp_host" class="block text-sm font-medium text-slate-700 mb-1.5">Servidor (host) <span class="text-red-500">*</span></label>
                    <input id="smtp_host" name="smtp_host" value="{{ old('smtp_host', $smtp['smtp_host']) }}" class="{{ $campo }} @error('smtp_host') border-red-400 @enderror" placeholder="Ex.: smtp.ciscopar.com.br">
                    @error('smtp_host') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="smtp_port" class="block text-sm font-medium text-slate-700 mb-1.5">Porta</label>
                    <input id="smtp_port" name="smtp_port" type="number" min="1" max="65535" value="{{ old('smtp_port', $smtp['smtp_port']) }}" class="{{ $campo }} @error('smtp_port') border-red-400 @enderror" placeholder="587">
                    @error('smtp_port') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-3">
                <div>
                    <label for="smtp_encryption" class="block text-sm font-medium text-slate-700 mb-1.5">Criptografia</label>
                    @php($enc = old('smtp_encryption', $smtp['smtp_encryption']))
                    <select id="smtp_encryption" name="smtp_encryption" class="{{ $campo }}">
                        <option value="" @selected($enc === null || $enc === '')>Nenhuma</option>
                        <option value="tls" @selected($enc === 'tls')>TLS (STARTTLS · 587)</option>
                        <option value="ssl" @selected($enc === 'ssl')>SSL (implícito · 465)</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label for="smtp_username" class="block text-sm font-medium text-slate-700 mb-1.5">Usuário</label>
                    <input id="smtp_username" name="smtp_username" value="{{ old('smtp_username', $smtp['smtp_username']) }}" class="{{ $campo }} @error('smtp_username') border-red-400 @enderror" autocomplete="off" placeholder="Ex.: nao-responder@ciscopar.com.br">
                    @error('smtp_username') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="smtp_password" class="block text-sm font-medium text-slate-700 mb-1.5">Senha</label>
                <input id="smtp_password" name="smtp_password" type="password" value="" class="{{ $campo }} @error('smtp_password') border-red-400 @enderror" autocomplete="new-password" placeholder="{{ $smtp['smtp_password'] ? '•••••••• (deixe em branco para manter)' : 'Senha do usuário SMTP' }}">
                @error('smtp_password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                <p class="mt-1 text-xs text-slate-500">Armazenada de forma cifrada. Deixe em branco para não alterar.</p>
            </div>

            <div class="grid gap-5 sm:grid-cols-2 pt-2 border-t border-slate-100">
                <div>
                    <label for="smtp_from_address" class="block text-sm font-medium text-slate-700 mb-1.5">E-mail remetente</label>
                    <input id="smtp_from_address" name="smtp_from_address" value="{{ old('smtp_from_address', $smtp['smtp_from_address']) }}" class="{{ $campo }} @error('smtp_from_address') border-red-400 @enderror" placeholder="nao-responder@ciscopar.com.br">
                    @error('smtp_from_address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="smtp_from_name" class="block text-sm font-medium text-slate-700 mb-1.5">Nome remetente</label>
                    <input id="smtp_from_name" name="smtp_from_name" value="{{ old('smtp_from_name', $smtp['smtp_from_name']) }}" class="{{ $campo }}" placeholder="Plataforma de Treinamentos">
                </div>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="submit" class="rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition-colors">Salvar configurações</button>
            </div>
        </div>
    </form>

    {{-- Envio de teste --}}
    <form method="POST" action="{{ route('gestao.configuracoes.testar') }}" class="mt-6">
        @csrf
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm max-w-2xl">
            <h2 class="font-semibold text-slate-800 mb-1">Testar envio</h2>
            <p class="text-sm text-slate-500 mb-4">Salve as configurações antes de testar. O e-mail usa os dados atuais.</p>
            <div class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[220px]">
                    <label for="email_teste" class="block text-sm font-medium text-slate-700 mb-1.5">Enviar para</label>
                    <input id="email_teste" name="email_teste" type="email" value="{{ old('email_teste', auth()->user()->email) }}" required class="{{ $campo }} @error('email_teste') border-red-400 @enderror" placeholder="voce@ciscopar.com.br">
                    @error('email_teste') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="rounded-lg border border-brand-600 px-4 py-2.5 text-sm font-semibold text-brand-700 hover:bg-brand-50 transition-colors">Enviar e-mail de teste</button>
            </div>
        </div>
    </form>

    {{-- Videoconferência (Jitsi) --}}
    <div class="mt-10 mb-6 flex items-start justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Videoconferência (Jitsi)</h2>
            <p class="text-sm text-slate-500">Usada para as salas de treinamentos online. Os dados devem casar com o servidor Jitsi.</p>
        </div>
        @if ($jitsiConfigurado)
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Configurado
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Não configurado
            </span>
        @endif
    </div>

    <form method="POST" action="{{ route('gestao.configuracoes.jitsi') }}">
        @csrf @method('PUT')
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm space-y-5 max-w-2xl">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="jitsi_domain" class="block text-sm font-medium text-slate-700 mb-1.5">Domínio <span class="text-red-500">*</span></label>
                    <input id="jitsi_domain" name="jitsi_domain" value="{{ old('jitsi_domain', $jitsi['jitsi_domain']) }}" class="{{ $campo }} @error('jitsi_domain') border-red-400 @enderror" placeholder="meet.ciscopar.com.br">
                    @error('jitsi_domain') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="jitsi_app_id" class="block text-sm font-medium text-slate-700 mb-1.5">App ID</label>
                    <input id="jitsi_app_id" name="jitsi_app_id" value="{{ old('jitsi_app_id', $jitsi['jitsi_app_id']) }}" class="{{ $campo }}" placeholder="ciscopar">
                </div>
            </div>
            <div>
                <label for="jitsi_app_secret" class="block text-sm font-medium text-slate-700 mb-1.5">Segredo (App Secret)</label>
                <input id="jitsi_app_secret" name="jitsi_app_secret" type="password" value="" autocomplete="new-password" class="{{ $campo }} @error('jitsi_app_secret') border-red-400 @enderror" placeholder="{{ $jitsi['jitsi_app_secret'] ? '•••••••• (deixe em branco para manter)' : 'JWT_APP_SECRET do jitsi.conf' }}">
                @error('jitsi_app_secret') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                <p class="mt-1 text-xs text-slate-500">É o <code>JWT_APP_SECRET</code> do servidor Jitsi. Armazenado cifrado. Deixe em branco para não alterar.</p>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition-colors">Salvar videoconferência</button>
            </div>
        </div>
    </form>
@endsection
