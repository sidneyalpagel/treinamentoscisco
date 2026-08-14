@php
    $inputBase = 'w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none';
    $inputErro = 'border-red-400 focus:border-red-500 focus:ring-red-500/30';
@endphp

<div class="grid gap-6 lg:grid-cols-3">

    {{-- Coluna principal --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm space-y-5">
            <h2 class="font-semibold text-slate-800">Informações do treinamento</h2>

            <div>
                <label for="titulo" class="block text-sm font-medium text-slate-700 mb-1.5">Título <span class="text-red-500">*</span></label>
                <input id="titulo" name="titulo" value="{{ old('titulo', $treinamento->titulo) }}" required
                       class="{{ $inputBase }} @error('titulo') {{ $inputErro }} @enderror"
                       placeholder="Ex.: Atendimento humanizado ao cidadão">
                @error('titulo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="descricao" class="block text-sm font-medium text-slate-700 mb-1.5">Descrição</label>
                <textarea id="descricao" name="descricao" rows="5"
                          class="{{ $inputBase }} @error('descricao') {{ $inputErro }} @enderror"
                          placeholder="Objetivos, conteúdo programático e demais informações.">{{ old('descricao', $treinamento->descricao) }}</textarea>
                @error('descricao') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="publico_alvo" class="block text-sm font-medium text-slate-700 mb-1.5">Público-alvo</label>
                <input id="publico_alvo" name="publico_alvo" value="{{ old('publico_alvo', $treinamento->publico_alvo) }}"
                       class="{{ $inputBase }} @error('publico_alvo') {{ $inputErro }} @enderror"
                       placeholder="Ex.: Servidores da área da saúde">
                @error('publico_alvo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="instrutor" class="block text-sm font-medium text-slate-700 mb-1.5">Instrutor(a)</label>
                    <input id="instrutor" name="instrutor" value="{{ old('instrutor', $treinamento->instrutor) }}"
                           class="{{ $inputBase }} @error('instrutor') {{ $inputErro }} @enderror">
                    @error('instrutor') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="carga_horaria" class="block text-sm font-medium text-slate-700 mb-1.5">Carga horária (horas)</label>
                    <input id="carga_horaria" name="carga_horaria" type="number" min="1" max="9999" value="{{ old('carga_horaria', $treinamento->carga_horaria) }}"
                           class="{{ $inputBase }} @error('carga_horaria') {{ $inputErro }} @enderror" placeholder="Ex.: 8">
                    @error('carga_horaria') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm space-y-5">
            <h2 class="font-semibold text-slate-800">Local e período</h2>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="modalidade" class="block text-sm font-medium text-slate-700 mb-1.5">Modalidade <span class="text-red-500">*</span></label>
                    <select id="modalidade" name="modalidade" onchange="toggleSalaOnline()" class="{{ $inputBase }} @error('modalidade') {{ $inputErro }} @enderror">
                        @foreach ($modalidadesDisponiveis as $valor => $rotulo)
                            <option value="{{ $valor }}" @selected(old('modalidade', $treinamento->modalidade) === $valor)>{{ $rotulo }}</option>
                        @endforeach
                    </select>
                    @error('modalidade') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="local" class="block text-sm font-medium text-slate-700 mb-1.5">Local</label>
                    <input id="local" name="local" value="{{ old('local', $treinamento->local) }}"
                           class="{{ $inputBase }} @error('local') {{ $inputErro }} @enderror" placeholder="Ex.: Auditório / Link da sala">
                    @error('local') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="data_inicio" class="block text-sm font-medium text-slate-700 mb-1.5">Início <span class="text-red-500">*</span></label>
                    <input id="data_inicio" name="data_inicio" type="datetime-local" required
                           value="{{ old('data_inicio', optional($treinamento->data_inicio)->format('Y-m-d\TH:i')) }}"
                           class="{{ $inputBase }} @error('data_inicio') {{ $inputErro }} @enderror">
                    @error('data_inicio') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="data_fim" class="block text-sm font-medium text-slate-700 mb-1.5">Término</label>
                    <input id="data_fim" name="data_fim" type="datetime-local"
                           value="{{ old('data_fim', optional($treinamento->data_fim)->format('Y-m-d\TH:i')) }}"
                           class="{{ $inputBase }} @error('data_fim') {{ $inputErro }} @enderror">
                    @error('data_fim') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            @if (! empty($mostrarCriarSala))
                <div id="sala-online-wrapper" class="pt-1" style="display:none;">
                    <label class="flex items-start gap-2.5 text-sm text-slate-700 select-none rounded-lg border border-brand-200 bg-brand-50 p-3">
                        <input type="hidden" name="criar_sala" value="0">
                        <input type="checkbox" name="criar_sala" value="1" @checked(old('criar_sala', true))
                               class="mt-0.5 rounded border-slate-300 text-brand-700 focus:ring-brand-500/30">
                        <span class="text-brand-900">Criar sala de videoconferência
                            <span class="block text-xs text-brand-800/80">Gera a sala online e os links (moderador e participantes) desta reunião agendada ao salvar.</span>
                        </span>
                    </label>
                </div>
            @endif
        </div>
    </div>

    {{-- Coluna lateral: publicação --}}
    <div class="space-y-6">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm space-y-5">
            <h2 class="font-semibold text-slate-800">Publicação</h2>

            <div>
                <label for="status" class="block text-sm font-medium text-slate-700 mb-1.5">Status</label>
                <select id="status" name="status" class="{{ $inputBase }} @error('status') {{ $inputErro }} @enderror">
                    @foreach ($statusDisponiveis as $valor => $rotulo)
                        <option value="{{ $valor }}" @selected(old('status', $treinamento->status) === $valor)>{{ $rotulo }}</option>
                    @endforeach
                </select>
                <p class="mt-1.5 text-xs text-slate-500">Apenas treinamentos <strong>publicados</strong> aparecem na agenda pública.</p>
                @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="vagas" class="block text-sm font-medium text-slate-700 mb-1.5">Vagas</label>
                <input id="vagas" name="vagas" type="number" min="1" value="{{ old('vagas', $treinamento->vagas) }}"
                       class="{{ $inputBase }} @error('vagas') {{ $inputErro }} @enderror" placeholder="Deixe em branco para ilimitado">
                @error('vagas') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="inscricoes_ate" class="block text-sm font-medium text-slate-700 mb-1.5">Inscrições até</label>
                <input id="inscricoes_ate" name="inscricoes_ate" type="date"
                       value="{{ old('inscricoes_ate', optional($treinamento->inscricoes_ate)->format('Y-m-d')) }}"
                       class="{{ $inputBase }} @error('inscricoes_ate') {{ $inputErro }} @enderror">
                @error('inscricoes_ate') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-start gap-2.5 text-sm text-slate-700 select-none">
                <input type="hidden" name="permite_inscricao" value="0">
                <input type="checkbox" name="permite_inscricao" value="1" @checked(old('permite_inscricao', $treinamento->permite_inscricao ?? true))
                       class="mt-0.5 rounded border-slate-300 text-brand-700 focus:ring-brand-500/30">
                <span>Permitir inscrições online<span class="block text-xs text-slate-500">Exibe o formulário de inscrição na página pública.</span></span>
            </label>

            <label class="flex items-start gap-2.5 text-sm text-slate-700 select-none">
                <input type="hidden" name="gera_certificado" value="0">
                <input type="checkbox" name="gera_certificado" value="1" @checked(old('gera_certificado', $treinamento->gera_certificado ?? true))
                       class="mt-0.5 rounded border-slate-300 text-brand-700 focus:ring-brand-500/30">
                <span>Emitir certificado<span class="block text-xs text-slate-500">Habilita a emissão de certificados para os participantes.</span></span>
            </label>
        </div>

        <div class="flex flex-col gap-2">
            <button type="submit" class="w-full rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition-colors">
                {{ $textoBotao ?? 'Salvar treinamento' }}
            </button>
            <a href="{{ route('admin.treinamentos.index') }}" class="w-full text-center rounded-lg px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors">
                Cancelar
            </a>
        </div>
    </div>
</div>

<script>
    function toggleSalaOnline() {
        var wrapper = document.getElementById('sala-online-wrapper');
        if (!wrapper) return;
        var modalidade = document.getElementById('modalidade').value;
        wrapper.style.display = (modalidade === 'online' || modalidade === 'hibrido') ? '' : 'none';
    }
    toggleSalaOnline();
</script>
