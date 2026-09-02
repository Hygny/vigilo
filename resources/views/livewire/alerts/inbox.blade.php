<div class="py-8">
    <div class="mx-auto max-w-[1200px] space-y-6 px-5 sm:px-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-[28px] font-bold tracking-[-0.03em] text-ink">Alertas</h1>
                <p class="mt-1 text-[14.5px] text-ink-muted">Mudanças detectadas que ainda não foram revisadas.</p>
            </div>
            <x-ui.button variant="secondary" size="sm" icon="done_all"
                         wire:click="acknowledgeAll" wire:confirm="Marcar como vistos todos os alertas do filtro atual?">
                Marcar todos como vistos
            </x-ui.button>
        </div>

        @if (session('status'))
            <div class="flex items-center gap-2 rounded-btn border border-line bg-surface px-4 py-3 text-sm text-ink-2 shadow-card">
                <x-ui.icon name="check_circle" :size="18" class="text-ok" />{{ session('status') }}
            </div>
        @endif

        <div class="flex flex-wrap gap-2">
            @foreach (['all' => 'Todos', 'critical' => 'Críticos', 'high' => 'Altos', 'medium' => 'Médios', 'low' => 'Baixos'] as $value => $label)
                <button wire:click="setSeverity('{{ $value }}')"
                        @class([
                            'rounded-full px-4 py-2 text-[13.5px] font-semibold transition-colors border cursor-pointer',
                            'bg-primary text-onprimary border-primary' => $severity === $value,
                            'bg-surface text-ink-2 border-line-strong hover:bg-surface-2' => $severity !== $value,
                        ])>
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div class="flex flex-col gap-3">
            @forelse ($events as $event)
                @php $tone = $event->severity->tone(); @endphp
                <div class="flex items-center gap-4 rounded-[14px] border border-line bg-surface p-4 shadow-card sm:px-5"
                     style="border-left:3px solid var(--{{ $tone }});">
                    <div class="flex h-[42px] w-[42px] shrink-0 items-center justify-center rounded-[11px]"
                         style="background:var(--{{ $tone }}-soft);color:var(--{{ $tone }});">
                        <x-ui.icon :name="$event->type->icon()" :size="23" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-x-2.5 gap-y-0.5">
                            <span class="text-[15px] font-semibold text-ink">{{ $event->monitoredCompany->label ?? $event->monitoredCompany->formattedCnpj() }}</span>
                            <span class="font-mono text-[11.5px] text-ink-muted">{{ $event->monitoredCompany->formattedCnpj() }}</span>
                        </div>
                        <p class="mt-0.5 text-[13.5px] text-ink-2">
                            {{ $event->type->label() }}:
                            <span class="text-ink-muted">{{ $event->old_value ?? '—' }}</span>
                            <x-ui.icon name="arrow_right_alt" :size="15" class="align-middle text-ink-muted" />
                            <span class="font-medium text-ink">{{ $event->new_value ?? '—' }}</span>
                        </p>
                    </div>
                    <x-ui.badge :tone="$tone" dot class="hidden sm:inline-flex">{{ $event->severity->label() }}</x-ui.badge>
                    <span class="hidden shrink-0 font-mono text-xs text-ink-muted md:inline">{{ $event->detected_at->format('d/m/Y H:i') }}</span>
                    <button wire:click="acknowledge({{ $event->id }})" title="Marcar como visto"
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-[9px] border border-line-strong bg-surface text-ink-2 transition-colors hover:bg-surface-2 cursor-pointer">
                        <x-ui.icon name="done" :size="19" />
                    </button>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center gap-3 rounded-card border border-line bg-surface p-16 text-center shadow-card">
                    <div class="flex h-[54px] w-[54px] items-center justify-center rounded-[14px] bg-ok-soft">
                        <x-ui.icon name="task_alt" :size="30" class="text-ok" />
                    </div>
                    <div class="text-[15px] font-semibold text-ink">Nenhum alerta neste filtro</div>
                    <div class="text-[13.5px] text-ink-muted">Tudo revisado por aqui.</div>
                </div>
            @endforelse
        </div>
    </div>
</div>
