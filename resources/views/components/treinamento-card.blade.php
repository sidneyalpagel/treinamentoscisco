@props(['treinamento'])

<a href="{{ route('treinamentos.show', $treinamento) }}"
   class="group flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-md hover:border-brand-300 transition-all overflow-hidden">
    <div class="bg-brand-800 text-white px-5 py-4 flex items-center justify-between">
        <div class="text-center leading-none">
            <div class="text-2xl font-bold">{{ $treinamento->data_inicio->format('d') }}</div>
            <div class="text-xs uppercase tracking-wide text-brand-200">{{ $treinamento->data_inicio->translatedFormat('M') }}</div>
        </div>
        <span class="rounded-full bg-white/10 px-2.5 py-1 text-xs font-medium">{{ $treinamento->modalidade_label }}</span>
    </div>
    <div class="p-5 flex-1 flex flex-col">
        <h3 class="font-semibold text-slate-800 group-hover:text-brand-700 transition-colors line-clamp-2">{{ $treinamento->titulo }}</h3>
        @if ($treinamento->descricao)
            <p class="mt-2 text-sm text-slate-500 line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($treinamento->descricao), 120) }}</p>
        @endif
        <div class="mt-4 pt-4 border-t border-slate-100 space-y-1.5 text-sm text-slate-500">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-brand-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                {{ $treinamento->data_inicio->translatedFormat('H:i') }}
                @if ($treinamento->carga_horaria) · {{ $treinamento->carga_horaria }}h @endif
            </div>
            @if ($treinamento->local)
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                    <span class="truncate">{{ $treinamento->local }}</span>
                </div>
            @endif
        </div>
        <span class="mt-4 inline-flex items-center gap-1.5 text-sm font-medium text-brand-700">
            Ver detalhes
            <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
        </span>
    </div>
</a>
