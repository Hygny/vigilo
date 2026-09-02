<div class="py-8">
    <div class="mx-auto max-w-[1200px] space-y-6 px-5 sm:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-[28px] font-bold tracking-[-0.03em] text-ink">Painel</h1>
                <p class="mt-1 text-[14.5px] text-ink-muted">Due diligence contínua da sua carteira de CNPJs.</p>
            </div>
            <x-ui.button :href="route('alerts.index')" wire:navigate icon="notifications">Ver alertas</x-ui.button>
        </div>

        @if (session('status'))
            <div class="flex items-center gap-2 rounded-btn border border-line bg-surface px-4 py-3 text-sm text-ink-2 shadow-card">
                <x-ui.icon name="check_circle" :size="18" class="text-ok" />{{ session('status') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-card border border-line bg-surface p-5 shadow-card">
                <div class="mb-3.5 flex items-center gap-2.5"><x-ui.icon name="business" :size="20" class="text-accent" /><span class="text-[13px] font-medium text-ink-muted">Empresas monitoradas</span></div>
                <div class="text-[34px] font-bold tracking-[-0.03em] text-ink">{{ $monitoredCount }}</div>
            </div>
            <div class="rounded-card border border-line bg-surface p-5 shadow-card">
                <div class="mb-3.5 flex items-center gap-2.5"><x-ui.icon name="folder_open" :size="20" class="text-accent" /><span class="text-[13px] font-medium text-ink-muted">Portfólios</span></div>
                <div class="text-[34px] font-bold tracking-[-0.03em] text-ink">{{ $portfoliosCount }}</div>
            </div>
            <div class="rounded-card border border-hl bg-hl p-5 shadow-card">
                <div class="mb-3.5 flex items-center gap-2.5"><x-ui.icon name="notifications" :size="20" class="text-hl-ink" /><span class="text-[13px] font-medium text-hl-ink">Alertas abertos</span></div>
                <div class="text-[34px] font-bold tracking-[-0.03em] text-hl-ink">{{ $openAlertsCount }}</div>
            </div>
            <div class="rounded-card border border-crit-soft bg-surface p-5 shadow-card">
                <div class="mb-3.5 flex items-center gap-2.5"><x-ui.icon name="gpp_maybe" :size="20" class="text-ink" /><span class="text-[13px] font-medium text-ink-muted">Alertas críticos abertos</span></div>
                <div class="text-[34px] font-bold tracking-[-0.03em] text-ink">{{ $openCriticalCount }}</div>
            </div>
        </div>

        <x-ui.card>
            <div class="flex items-center justify-between border-b border-line px-[22px] py-4">
                <div class="flex items-center gap-2.5"><x-ui.icon name="history" :size="20" class="text-accent" /><h2 class="text-base font-semibold tracking-[-0.01em] text-ink">Últimas mudanças</h2></div>
                <span class="font-mono text-xs text-ink-muted">atualiza automaticamente</span>
            </div>
            @forelse ($recentChanges as $change)
                <div class="flex items-center gap-4 border-b border-line px-[22px] py-3.5 last:border-b-0">
                    <div class="min-w-0 flex-1">
                        <a href="{{ route('companies.show', $change->monitored_company_id) }}" wire:navigate
                           class="truncate text-sm font-semibold text-accent hover:underline">
                            {{ $change->monitoredCompany->label ?? $change->monitoredCompany->formattedCnpj() }}
                        </a>
                        <p class="mt-0.5 truncate text-[13.5px] text-ink-2">
                            {{ $change->type->label() }}
                            @if ($change->old_value !== null || $change->new_value !== null)
                                <span class="text-ink-muted line-through">{{ $change->old_value ?? '—' }}</span>
                                <x-ui.icon name="arrow_right_alt" :size="15" class="align-middle text-ink-muted" />
                                <span class="font-medium text-ink">{{ $change->new_value ?? '—' }}</span>
                            @endif
                        </p>
                    </div>
                    <x-ui.badge :tone="$change->severity->tone()" dot>{{ $change->severity->label() }}</x-ui.badge>
                    <span class="shrink-0 text-right font-mono text-xs text-ink-muted">{{ $change->detected_at->diffForHumans() }}</span>
                </div>
            @empty
                <div class="px-[22px] py-10 text-center text-sm text-ink-muted">
                    Nenhuma mudança detectada ainda. Adicione empresas a um portfólio e clique em “Atualizar”.
                </div>
            @endforelse
        </x-ui.card>
    </div>
</div>
