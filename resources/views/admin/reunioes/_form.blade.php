@php($campo = 'w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none')

<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm space-y-5 max-w-2xl">
    <div>
        <label for="titulo" class="block text-sm font-medium text-slate-700 mb-1.5">Título <span class="text-red-500">*</span></label>
        <input id="titulo" name="titulo" value="{{ old('titulo', $reuniao->titulo) }}" required class="{{ $campo }} @error('titulo') border-red-400 @enderror" placeholder="Ex.: Reunião de alinhamento — Setor de Saúde">
        @error('titulo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="descricao" class="block text-sm font-medium text-slate-700 mb-1.5">Descrição / pauta</label>
        <textarea id="descricao" name="descricao" rows="4" class="{{ $campo }}">{{ old('descricao', $reuniao->descricao) }}</textarea>
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="data_inicio" class="block text-sm font-medium text-slate-700 mb-1.5">Início <span class="text-red-500">*</span></label>
            <input id="data_inicio" name="data_inicio" type="datetime-local" required
                   value="{{ old('data_inicio', optional($reuniao->data_inicio)->format('Y-m-d\TH:i')) }}"
                   class="{{ $campo }} @error('data_inicio') border-red-400 @enderror">
            @error('data_inicio') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="data_fim" class="block text-sm font-medium text-slate-700 mb-1.5">Término</label>
            <input id="data_fim" name="data_fim" type="datetime-local"
                   value="{{ old('data_fim', optional($reuniao->data_fim)->format('Y-m-d\TH:i')) }}"
                   class="{{ $campo }} @error('data_fim') border-red-400 @enderror">
            @error('data_fim') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-2 pt-2">
        <button type="submit" class="rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition-colors">{{ $textoBotao ?? 'Salvar' }}</button>
        @if (! empty($permitirAgora))
            <button type="submit" formaction="{{ route('admin.reunioes.agora') }}" formnovalidate
                    class="inline-flex items-center gap-2 rounded-lg border border-emerald-600 px-4 py-2.5 text-sm font-semibold text-emerald-700 hover:bg-emerald-50 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z"/></svg>
                Iniciar agora
            </button>
        @endif
        <a href="{{ route('admin.reunioes.index') }}" class="rounded-lg px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors">Cancelar</a>
    </div>
    @if (! empty($permitirAgora))
        <p class="text-xs text-slate-500">“Iniciar agora” cria a reunião na hora, sem precisar de data.</p>
    @endif
</div>
