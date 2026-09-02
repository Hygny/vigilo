@props([
    'tone' => 'low',
    'icon' => null,
    'dot' => false,
])

@php
    $tones = [
        'crit'    => 'text-crit bg-crit-soft',
        'high'    => 'text-high bg-high-soft',
        'med'     => 'text-med bg-med-soft',
        'low'     => 'text-low bg-low-soft',
        'ok'      => 'text-ok bg-ok-soft',
        'muted'   => 'text-ink-muted bg-surface-2 border border-line',
    ];
    $classes = 'inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold leading-none'
        .' '.($tones[$tone] ?? $tones['low']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if ($dot)<span class="h-[7px] w-[7px] rounded-full" style="background: currentColor;"></span>@endif
    @if ($icon)<x-ui.icon :name="$icon" :size="15" />@endif
    {{ $slot }}
</span>
