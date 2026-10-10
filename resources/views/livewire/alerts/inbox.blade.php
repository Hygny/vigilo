@use('App\Enums\TriageStatus')

<div class="py-8">
    <div class="mx-auto max-w-[1200px] space-y-6 px-5 sm:px-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-[28px] font-bold tracking-[-0.03em] text-ink">Alertas</h1>
                <p class="mt-1 text-[14.5px] text-ink-muted">Triagem das mudanças detectadas: novo → em análise → descartado ou caso.</p>
            </div>
            <x-ui.button variant="secondary" size="sm" icon="download" wire:click="export">
                Exportar Excel
            </x-ui.button>
        </div>

        @if (session('status'))
            <div class="flex items-center gap-2 rounded-btn border border-line bg-surface px-4 py-3 text-sm text-ink-2 shadow-card">
                <x-ui.icon name="check_circle" :size="18" class="text-ok" />{{ session('status') }}
            </div>
        @endif

        {{-- Filtro de triagem --}}
        <div class="flex flex-wrap gap-2">
            @foreach (['abertos' => 'Em aberto', 'casos' => 'Casos', 'descartados' => 'Descartados', 'todos' => 'Todos'] as $value => $label)
                <button wire:click="setTriage('{{ $value }}')"
                        @class([
                            'rounded-full px-4 py-2 text-[13.5px] font-semibold transition-colors border cursor-pointer',
                            'bg-primary text-onprimary border-primary' => $triage === $value,
                            'bg-surface text-ink-2 border-line-strong hover:bg-surface-2' => $triage !== $value,
                        ])>
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Filtro de severidade --}}
        <div class="flex flex-wrap gap-2">
            @foreach (['all' => 'Todas severidades', 'critical' => 'Críticos', 'high' => 'Altos', 'medium' => 'Médios', 'low' => 'Baixos'] as $value => $label)
                <button wire:click="setSeverity('{{ $value }}')"
                        @class([
                            'rounded-full px-3.5 py-1.5 text-[12.5px] font-medium transition-colors border cursor-pointer',
                            'bg-ink text-surface border-ink' => $severity === $value,
                            'bg-surface text-ink-muted border-line hover:bg-surface-2' => $severity !== $value,
                        ])>
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Seleção múltipla + ações em lote --}}
        @if ($selectableIds !== [])
            <div x-data="{}" class="flex flex-wrap items-center gap-x-4 gap-y-3 rounded-[12px] border border-line bg-surface px-4 py-3 shadow-card">
                <label class="flex cursor-pointer items-center gap-2 text-[13px] font-medium text-ink-2">
                    {{-- .checked/.indeterminate vêm do estado real (wire:selected),
                         não do atributo HTML — o morph do Livewire não atualiza a
                         propriedade de um checkbox fora de wire:model, então ele
                         ficava "preso" marcado após uma ação em lote. --}}
                    <input type="checkbox" wire:click="toggleSelectAll" @checked($allSelected)
                           x-effect="
                               $el.checked = $wire.selected.length === {{ count($selectableIds) }};
                               $el.indeterminate = $wire.selected.length > 0 && $wire.selected.length < {{ count($selectableIds) }};
                           "
                           class="h-4 w-4 rounded border-line-strong bg-surface-2 text-primary focus:ring-focus cursor-pointer">
                    Selecionar todos ({{ count($selectableIds) }})
                </label>

                @if ($selectedCount > 0)
                    <span class="text-[13px] font-semibold text-ink">{{ $selectedCount }} selecionado(s)</span>
                    <div class="flex flex-wrap items-center gap-2">
                        <x-ui.button size="sm" variant="secondary" icon="search" wire:click="bulkStartAnalysis">Iniciar análise</x-ui.button>
                        <x-ui.button size="sm" variant="secondary" icon="flag" wire:click="bulkPromoteToCase">Virar caso</x-ui.button>
                        <x-ui.button size="sm" variant="danger" icon="block" wire:click="beginBulkDismiss">Descartar</x-ui.button>
                        <button type="button" wire:click="clearSelection"
                                class="text-[13px] text-ink-muted underline-offset-2 hover:underline cursor-pointer">Limpar</button>
                    </div>
                @endif

                @if ($bulkDismissing)
                    <div class="w-full border-t border-line pt-3">
                        <label class="mb-1 block text-[12.5px] font-medium text-ink-2">Motivo do descarte (aplicado a todos os selecionados)</label>
                        <div class="flex flex-col gap-2 sm:flex-row">
                            <input type="text" wire:model="bulkDismissReason" wire:keydown.enter="confirmBulkDismiss"
                                   class="ui-input flex-1" placeholder="Ex.: lote de filiais encerradas" autofocus>
                            <div class="flex gap-2">
                                <x-ui.button size="sm" variant="danger" wire:click="confirmBulkDismiss">Descartar {{ $selectedCount }}</x-ui.button>
                                <x-ui.button size="sm" variant="secondary" wire:click="cancelBulkDismiss">Cancelar</x-ui.button>
                            </div>
                        </div>
                        @error('bulkDismissReason') <span class="mt-1 block text-xs text-danger">{{ $message }}</span> @enderror
                    </div>
                @endif
            </div>
        @endif

        <div class="flex flex-col gap-3">
            @forelse ($events as $event)
                @php $tone = $event->severity->tone(); $st = $event->triage_status; @endphp
                <div wire:key="event-{{ $event->id }}"
                     class="rounded-[14px] border border-line bg-surface p-4 shadow-card sm:px-5"
                     style="border-left:3px solid var(--{{ $tone }});">
                    <div class="flex items-center gap-4">
                        @if ($st->isOpen())
                            <input type="checkbox" value="{{ $event->id }}" wire:model.live="selected"
                                   aria-label="Selecionar alerta"
                                   class="h-4 w-4 shrink-0 rounded border-line-strong bg-surface-2 text-primary focus:ring-focus cursor-pointer">
                        @endif
                        <div class="flex h-[42px] w-[42px] shrink-0 items-center justify-center rounded-[11px]"
                             style="background:var(--{{ $tone }}-soft);color:var(--{{ $tone }});">
                            <x-ui.icon :name="$event->type->icon()" :size="23" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-x-2.5 gap-y-0.5">
                                <span class="text-[15px] font-semibold text-ink">{{ $event->monitoredCompany->label ?? $event->monitoredCompany->formattedCnpj() }}</span>
                                <span class="font-mono text-[11.5px] text-ink-muted">{{ $event->monitoredCompany->formattedCnpj() }}</span>
                                <x-ui.badge :tone="$st->tone()" :icon="$st->icon()">{{ $st->label() }}</x-ui.badge>
                            </div>
                            <p class="mt-0.5 text-[13.5px] text-ink-2">
                                {{ $event->type->label() }}:
                                <span class="text-ink-muted">{{ $event->old_value ?? '—' }}</span>
                                <x-ui.icon name="arrow_right_alt" :size="15" class="align-middle text-ink-muted" />
                                <span class="font-medium text-ink">{{ $event->new_value ?? '—' }}</span>
                            </p>
                            @if ($st === TriageStatus::Descartado && $event->triage_reason)
                                <p class="mt-1 text-[12.5px] text-ink-muted"><span class="font-semibold">Motivo:</span> {{ $event->triage_reason }}</p>
                            @endif
                        </div>
                        <x-ui.badge :tone="$tone" dot class="hidden sm:inline-flex">{{ $event->severity->label() }}</x-ui.badge>
                        <span class="hidden shrink-0 font-mono text-xs text-ink-muted md:inline">{{ $event->detected_at->format('d/m/Y H:i') }}</span>

                        {{-- Ações de triagem --}}
                        <div class="flex shrink-0 items-center gap-1.5">
                            @if ($st === TriageStatus::Novo)
                                <button wire:click="startAnalysis({{ $event->id }})" title="Iniciar análise"
                                        class="flex h-9 w-9 items-center justify-center rounded-[9px] border border-line-strong bg-surface text-ink-2 transition-colors hover:bg-surface-2 cursor-pointer">
                                    <x-ui.icon name="search" :size="18" />
                                </button>
                            @endif
                            @if ($st->isOpen())
                                <button wire:click="promoteToCase({{ $event->id }})" title="Virar caso"
                                        class="flex h-9 w-9 items-center justify-center rounded-[9px] border border-line-strong bg-surface text-crit transition-colors hover:bg-crit-soft cursor-pointer">
                                    <x-ui.icon name="flag" :size="18" />
                                </button>
                                <button wire:click="beginDismiss({{ $event->id }})" title="Descartar"
                                        class="flex h-9 w-9 items-center justify-center rounded-[9px] border border-line-strong bg-surface text-ink-muted transition-colors hover:bg-surface-2 cursor-pointer">
                                    <x-ui.icon name="block" :size="18" />
                                </button>
                            @else
                                @if ($st === TriageStatus::Caso)
                                    <a href="{{ route('companies.graph', $event->monitoredCompany) }}" wire:navigate title="Ver no grafo"
                                       class="flex h-9 w-9 items-center justify-center rounded-[9px] border border-line-strong bg-surface text-accent transition-colors hover:bg-primary-soft cursor-pointer">
                                        <x-ui.icon name="hub" :size="18" />
                                    </a>
                                @endif
                                <button wire:click="reopen({{ $event->id }})" title="Reabrir"
                                        class="flex h-9 w-9 items-center justify-center rounded-[9px] border border-line-strong bg-surface text-ink-2 transition-colors hover:bg-surface-2 cursor-pointer">
                                    <x-ui.icon name="undo" :size="18" />
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Formulário de descarte (motivo obrigatório) --}}
                    @if ($dismissingId === $event->id)
                        <div class="mt-3 border-t border-line pt-3">
                            <label class="mb-1 block text-[12.5px] font-medium text-ink-2">Motivo do descarte</label>
                            <div class="flex flex-col gap-2 sm:flex-row">
                                <input type="text" wire:model="dismissReason" wire:keydown.enter="confirmDismiss"
                                       class="ui-input flex-1" placeholder="Ex.: filial encerrada, matriz ativa" autofocus>
                                <div class="flex gap-2">
                                    <x-ui.button size="sm" variant="danger" wire:click="confirmDismiss">Descartar</x-ui.button>
                                    <x-ui.button size="sm" variant="secondary" wire:click="cancelDismiss">Cancelar</x-ui.button>
                                </div>
                            </div>
                            @error('dismissReason') <span class="mt-1 block text-xs text-danger">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>
            @empty
                <div class="flex flex-col items-center justify-center gap-3 rounded-card border border-line bg-surface p-16 text-center shadow-card">
                    <div class="flex h-[54px] w-[54px] items-center justify-center rounded-[14px] bg-ok-soft">
                        <x-ui.icon name="task_alt" :size="30" class="text-ok" />
                    </div>
                    <div class="text-[15px] font-semibold text-ink">Nenhum alerta neste filtro</div>
                    <div class="text-[13.5px] text-ink-muted">Nada por aqui com os filtros atuais.</div>
                </div>
            @endforelse
        </div>
    </div>
</div>
