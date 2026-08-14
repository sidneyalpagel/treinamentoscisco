@php($campo = 'w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 focus:outline-none')

<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm space-y-5 max-w-2xl">
    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Nome <span class="text-red-500">*</span></label>
            <input id="name" name="name" value="{{ old('name', $usuario->name) }}" required class="{{ $campo }} @error('name') border-red-400 @enderror">
            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">E-mail <span class="text-red-500">*</span></label>
            <input id="email" name="email" type="email" value="{{ old('email', $usuario->email) }}" required class="{{ $campo }} @error('email') border-red-400 @enderror">
            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="role" class="block text-sm font-medium text-slate-700 mb-1.5">Papel <span class="text-red-500">*</span></label>
            <select id="role" name="role" class="{{ $campo }} @error('role') border-red-400 @enderror" onchange="alternarArea()">
                @foreach ($rolesDisponiveis as $valor => $rotulo)
                    <option value="{{ $valor }}" @selected(old('role', $usuario->role) === $valor)>{{ $rotulo }}</option>
                @endforeach
            </select>
            @error('role') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div id="area-wrapper">
            <label for="area_id" class="block text-sm font-medium text-slate-700 mb-1.5">Área / Setor <span class="text-red-500">*</span></label>
            <select id="area_id" name="area_id" class="{{ $campo }} @error('area_id') border-red-400 @enderror">
                <option value="">Selecione…</option>
                @foreach ($areas as $area)
                    <option value="{{ $area->id }}" @selected((string) old('area_id', $usuario->area_id) === (string) $area->id)>{{ $area->nome_completo }}</option>
                @endforeach
            </select>
            @error('area_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            @if ($areas->isEmpty())
                <p class="mt-1 text-xs text-amber-600">Nenhuma área cadastrada. <a href="{{ route('gestao.areas.create') }}" class="underline">Cadastre uma área</a> primeiro.</p>
            @endif
        </div>
    </div>

    @if (! empty($mostrarSenha))
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Senha inicial <span class="text-red-500">*</span></label>
                <input id="password" name="password" type="password" required class="{{ $campo }} @error('password') border-red-400 @enderror" placeholder="Mínimo 8 caracteres">
                @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">Confirmar senha <span class="text-red-500">*</span></label>
                <input id="password_confirmation" name="password_confirmation" type="password" required class="{{ $campo }}">
            </div>
        </div>
    @endif

    <label class="flex items-center gap-2.5 text-sm text-slate-700 select-none">
        <input type="hidden" name="ativo" value="0">
        <input type="checkbox" name="ativo" value="1" @checked(old('ativo', $usuario->ativo ?? true)) class="rounded border-slate-300 text-brand-700 focus:ring-brand-500/30">
        Acesso ativo (o usuário pode entrar na plataforma)
    </label>

    <div class="flex gap-2 pt-2">
        <button type="submit" class="rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 transition-colors">{{ $textoBotao ?? 'Salvar' }}</button>
        <a href="{{ route('gestao.usuarios.index') }}" class="rounded-lg px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors">Cancelar</a>
    </div>
</div>

<script>
    function alternarArea() {
        var role = document.getElementById('role').value;
        var wrapper = document.getElementById('area-wrapper');
        wrapper.style.display = (role === 'gestor') ? '' : 'none';
    }
    alternarArea();
</script>
