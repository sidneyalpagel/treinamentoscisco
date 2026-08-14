@php
    $itensMobile = [
        ['rota' => 'admin.dashboard', 'ativo' => 'admin.dashboard', 'rotulo' => 'Painel'],
        ['rota' => 'admin.treinamentos.index', 'ativo' => 'admin.treinamentos.*', 'rotulo' => 'Treinamentos'],
        ['rota' => 'admin.inscricoes.index', 'ativo' => 'admin.inscricoes.*', 'rotulo' => 'Inscrições'],
        ['rota' => 'admin.presenca.painel', 'ativo' => ['admin.presenca.*', 'admin.sessoes.*'], 'rotulo' => 'Presença'],
        ['rota' => 'admin.certificados.painel', 'ativo' => 'admin.certificados.*', 'rotulo' => 'Certificados'],
    ];
@endphp

@foreach ($itensMobile as $item)
    <a href="{{ route($item['rota']) }}"
       @class([
           'shrink-0 rounded-md px-3 py-1.5 text-sm font-medium transition-colors',
           'bg-brand-700 text-white' => request()->routeIs(...(array) $item['ativo']),
           'text-slate-600 bg-slate-100' => ! request()->routeIs(...(array) $item['ativo']),
       ])>
        {{ $item['rotulo'] }}
    </a>
@endforeach
