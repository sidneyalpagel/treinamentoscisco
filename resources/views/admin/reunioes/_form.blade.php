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

    <div class="flex gap-2 pt-2">
        <button type="submit" class="rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition-colors">{{ $textoBotao ?? 'Salvar' }}</button>
        <a href="{{ route('admin.reunioes.index') }}" class="rounded-lg px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors">Cancelar</a>
    </div>
</div>
