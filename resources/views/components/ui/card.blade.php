@props([])

<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-card border border-line bg-surface shadow-card']) }}>
    {{ $slot }}
</div>
