{{-- Cabeçalho da marca --}}
<div class="h-16 flex items-center gap-2.5 px-5 border-b border-white/10">
    <span class="grid place-items-center w-9 h-9 rounded-lg bg-white/10 text-white">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342"/></svg>
    </span>
    <div class="leading-tight">
        <div class="text-sm font-bold text-white">Treinamentos</div>
        <div class="text-[11px] text-brand-300">Painel administrativo</div>
    </div>
</div>

{{-- Navegação --}}
<nav class="flex-1 px-3 py-4 space-y-1">
    @php
        $itens = [
            ['rota' => 'admin.dashboard', 'ativo' => 'admin.dashboard', 'rotulo' => 'Painel', 'icone' => 'M2.25 12 11.204 3.045c.44-.44 1.152-.44 1.591 0L21.75 12M4.5 9.75v10.5a.75.75 0 0 0 .75.75H9.75v-6a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75v6h4.5a.75.75 0 0 0 .75-.75V9.75'],
            ['rota' => 'admin.treinamentos.index', 'ativo' => 'admin.treinamentos.*', 'rotulo' => 'Treinamentos', 'icone' => 'M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25'],
        ];
    @endphp

    @foreach ($itens as $item)
        <a href="{{ route($item['rota']) }}"
           @class([
               'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
               'bg-white/10 text-white' => request()->routeIs($item['ativo']),
               'text-brand-200 hover:bg-white/5 hover:text-white' => ! request()->routeIs($item['ativo']),
           ])>
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icone'] }}"/></svg>
            {{ $item['rotulo'] }}
        </a>
    @endforeach

    {{-- Módulos futuros --}}
    <div class="pt-4 mt-4 border-t border-white/10">
        <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-brand-400">Em breve</p>
        @foreach (['Inscrições', 'Lista de presença', 'Certificados'] as $modulo)
            <span class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-brand-300/60 cursor-not-allowed">
                <svg class="w-5 h-5 shrink-0 opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                {{ $modulo }}
            </span>
        @endforeach
    </div>
</nav>
