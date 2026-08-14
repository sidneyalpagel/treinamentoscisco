@props(['status', 'label' => null])

@php
    $mapa = [
        // Treinamentos
        'publicado' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'rascunho'  => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'encerrado' => 'bg-slate-100 text-slate-600 ring-slate-500/20',
        // Inscrições
        'confirmada' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'pendente'   => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'cancelada'  => 'bg-red-50 text-red-700 ring-red-600/20',
    ];
    $classe = $mapa[$status] ?? 'bg-slate-100 text-slate-600 ring-slate-500/20';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset $classe"]) }}>
    <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span>
    {{ $label ?? ucfirst($status) }}
</span>
