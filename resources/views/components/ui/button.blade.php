@props([
    'variant' => 'primary',
    'href' => null,
    'icon' => null,
    'size' => 'md',
])

@php
    $sizes = [
        'md' => 'px-4 py-2.5 text-sm',
        'sm' => 'px-3.5 py-2 text-[13px]',
    ];
    $variants = [
        'primary'   => 'bg-primary text-onprimary shadow-card hover:bg-primary-hover',
        'secondary' => 'bg-surface text-ink border border-line-strong hover:bg-surface-2',
        'ghost'     => 'bg-transparent text-accent hover:bg-primary-soft',
        'danger'    => 'bg-transparent text-crit border border-crit hover:bg-crit hover:text-crit-soft',
    ];
    $classes = 'inline-flex items-center justify-center gap-2 rounded-btn font-semibold transition-colors cursor-pointer'
        .' focus:outline-none focus-visible:ring-2 focus-visible:ring-focus disabled:opacity-60 disabled:cursor-not-allowed'
        .' '.($sizes[$size] ?? $sizes['md'])
        .' '.($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<x-ui.icon :name="$icon" :size="$size === 'sm' ? 17 : 18" />@endif
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => $classes]) }}>
        @if ($icon)<x-ui.icon :name="$icon" :size="$size === 'sm' ? 17 : 18" />@endif
        {{ $slot }}
    </button>
@endif
