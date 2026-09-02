@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full rounded-btn border-line-strong bg-surface-2 text-sm text-ink placeholder:text-ink-muted shadow-sm focus:border-hl focus:ring-focus']) }}>
