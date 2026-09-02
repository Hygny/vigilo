<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 rounded-btn bg-primary px-4 py-2.5 text-sm font-semibold text-onprimary shadow-card transition-colors hover:bg-primary-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-focus disabled:opacity-60']) }}>
    {{ $slot }}
</button>
