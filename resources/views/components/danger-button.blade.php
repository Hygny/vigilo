<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-btn border border-danger bg-transparent px-4 py-2.5 text-sm font-semibold text-danger transition-colors hover:bg-danger hover:text-danger-soft focus:outline-none focus-visible:ring-2 focus-visible:ring-focus']) }}>
    {{ $slot }}
</button>
