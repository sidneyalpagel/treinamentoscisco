@php($campo = 'w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none')

<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm space-y-5 max-w-2xl">
    <div class="grid gap-5 sm:grid-cols-3">
        <div class="sm:col-span-2">
            <label for="nome" class="block text-sm font-medium text-slate-700 mb-1.5">Nome <span class="text-red-500">*</span></label>
            <input id="nome" name="nome" value="{{ old('nome', $area->nome) }}" required class="{{ $campo }} @error('nome') border-red-400 @enderror" placeholder="Ex.: Atenção Primária à Saúde">
            @error('nome') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="sigla" class="block text-sm font-medium text-slate-700 mb-1.5">Sigla</label>
            <input id="sigla" name="sigla" value="{{ old('sigla', $area->sigla) }}" class="{{ $campo }} @error('sigla') border-red-400 @enderror" placeholder="Ex.: APS">
            @error('sigla') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>
    <div>
        <label for="descricao" class="block text-sm font-medium text-slate-700 mb-1.5">Descrição</label>
        <textarea id="descricao" name="descricao" rows="3" class="{{ $campo }}">{{ old('descricao', $area->descricao) }}</textarea>
    </div>
    <label class="flex items-center gap-2.5 text-sm text-slate-700 select-none">
        <input type="hidden" name="ativo" value="0">
        <input type="checkbox" name="ativo" value="1" @checked(old('ativo', $area->ativo ?? true)) class="rounded border-slate-300 text-brand-700 focus:ring-brand-500/30">
        Área ativa (disponível para vincular gestores)
    </label>

    <div class="flex gap-2 pt-2">
        <button type="submit" class="rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition-colors">{{ $textoBotao ?? 'Salvar' }}</button>
        <a href="{{ route('gestao.areas.index') }}" class="rounded-lg px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors">Cancelar</a>
    </div>
</div>
