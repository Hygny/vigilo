@props(['name', 'size' => 20])

<span {{ $attributes->merge(['class' => 'msym']) }} style="font-size: {{ $size }}px;" aria-hidden="true">{{ $name }}</span>
