<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-btn border border-crit bg-transparent px-4 py-2.5 text-sm font-semibold text-crit transition-colors hover:bg-crit hover:text-crit-soft focus:outline-none focus-visible:ring-2 focus-visible:ring-focus']) }}>
    {{ $slot }}
</button>
