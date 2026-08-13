@php
    $itensMobile = [
        ['rota' => 'admin.dashboard', 'ativo' => 'admin.dashboard', 'rotulo' => 'Painel'],
        ['rota' => 'admin.treinamentos.index', 'ativo' => 'admin.treinamentos.*', 'rotulo' => 'Treinamentos'],
    ];
@endphp

@foreach ($itensMobile as $item)
    <a href="{{ route($item['rota']) }}"
       @class([
           'shrink-0 rounded-md px-3 py-1.5 text-sm font-medium transition-colors',
           'bg-brand-700 text-white' => request()->routeIs($item['ativo']),
           'text-slate-600 bg-slate-100' => ! request()->routeIs($item['ativo']),
       ])>
        {{ $item['rotulo'] }}
    </a>
@endforeach
