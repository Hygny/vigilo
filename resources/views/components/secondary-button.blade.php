<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center rounded-btn border border-line-strong bg-surface px-4 py-2.5 text-sm font-semibold text-ink transition-colors hover:bg-surface-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-focus']) }}>
    {{ $slot }}
</button>
