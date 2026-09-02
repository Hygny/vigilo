<div class="py-8">
    <div class="mx-auto max-w-[1200px] space-y-6 px-5 sm:px-8">
        <div>
            <h1 class="text-[28px] font-bold tracking-[-0.03em] text-ink">Portfólios</h1>
            <p class="mt-1 text-[14.5px] text-ink-muted">Grupos de CNPJs monitorados de forma independente.</p>
        </div>

        @if (session('status'))
            <div class="flex items-center gap-2 rounded-btn border border-line bg-surface px-4 py-3 text-sm text-ink-2 shadow-card">
                <x-ui.icon name="check_circle" :size="18" class="text-ok" />{{ session('status') }}
            </div>
        @endif

        <form wire:submit="create" class="flex flex-col gap-3 rounded-card border border-line bg-surface p-5 shadow-card sm:flex-row sm:items-end">
            <div class="flex-1">
                <label for="name" class="mb-1.5 block text-[13px] font-semibold text-ink">Novo portfólio</label>
                <div class="ui-input-wrap">
                    <x-ui.icon name="create_new_folder" :size="19" class="text-ink-muted" />
                    <input wire:model="name" id="name" type="text" placeholder="Ex.: Rede Yázigi" class="ui-input" />
                </div>
                @error('name') <p class="mt-1 text-sm text-crit">{{ $message }}</p> @enderror
            </div>
            <x-ui.button type="submit" icon="add">Criar</x-ui.button>
        </form>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            @forelse ($portfolios as $portfolio)
                @if ($editingId === $portfolio->id)
                    <div class="rounded-card border border-line bg-surface p-5 shadow-card sm:col-span-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <div class="ui-input-wrap flex-1">
                                <x-ui.icon name="edit" :size="18" class="text-ink-muted" />
                                <input wire:model="editingName" type="text" class="ui-input" />
                            </div>
                            <x-ui.button wire:click="saveEdit" size="sm" icon="check">Salvar</x-ui.button>
                            <x-ui.button wire:click="cancelEdit" variant="secondary" size="sm">Cancelar</x-ui.button>
                        </div>
                        @error('editingName') <p class="mt-1 text-sm text-crit">{{ $message }}</p> @enderror
                    </div>
                @else
                    <div class="rounded-card border border-line bg-surface p-[22px] shadow-card transition-shadow hover:shadow-float">
                        <div class="mb-4 flex items-start justify-between gap-3">
                            <a href="{{ route('portfolios.show', $portfolio) }}" wire:navigate class="flex min-w-0 items-center gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-[11px] bg-primary-soft"><x-ui.icon name="folder_open" :size="22" class="text-accent" /></span>
                                <span class="min-w-0">
                                    <span class="block truncate text-[17px] font-semibold tracking-[-0.01em] text-ink hover:underline">{{ $portfolio->name }}</span>
                                    <span class="block text-[12.5px] text-ink-muted">{{ $portfolio->monitored_companies_count }} empresa(s) monitorada(s)</span>
                                </span>
                            </a>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <button wire:click="queueRefresh({{ $portfolio->id }})" title="Atualizar" class="ui-icon-btn"><x-ui.icon name="refresh" :size="18" /></button>
                                <button wire:click="startEdit({{ $portfolio->id }})" title="Renomear" class="ui-icon-btn"><x-ui.icon name="edit" :size="18" /></button>
                                <button wire:click="delete({{ $portfolio->id }})" wire:confirm="Remover este portfólio e todas as empresas nele?" title="Excluir"
                                        class="flex h-9 w-9 items-center justify-center rounded-[9px] border border-crit bg-transparent text-crit transition-colors hover:bg-crit hover:text-crit-soft cursor-pointer"><x-ui.icon name="delete" :size="18" /></button>
                            </div>
                        </div>
                        <div class="grid grid-cols-4 gap-2.5">
                            <div class="rounded-[10px] border border-line bg-surface-2 px-3 py-2.5"><div class="text-[11px] font-semibold uppercase tracking-wide text-ink-muted">Atualiz.</div><div class="mt-0.5 text-[19px] font-bold text-ok">{{ $portfolio->ok_count }}</div></div>
                            <div class="rounded-[10px] border border-line bg-surface-2 px-3 py-2.5"><div class="text-[11px] font-semibold uppercase tracking-wide text-ink-muted">Pendentes</div><div class="mt-0.5 text-[19px] font-bold text-ink">{{ $portfolio->pending_count }}</div></div>
                            <div class="rounded-[10px] border border-line bg-surface-2 px-3 py-2.5"><div class="text-[11px] font-semibold uppercase tracking-wide text-ink-muted">N/ enc.</div><div class="mt-0.5 text-[19px] font-bold text-high">{{ $portfolio->not_found_count }}</div></div>
                            <div class="rounded-[10px] border border-line bg-surface-2 px-3 py-2.5"><div class="text-[11px] font-semibold uppercase tracking-wide text-ink-muted">Erros</div><div class="mt-0.5 text-[19px] font-bold text-crit">{{ $portfolio->error_count }}</div></div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="rounded-card border border-line bg-surface px-6 py-12 text-center text-sm text-ink-muted shadow-card sm:col-span-2">
                    Nenhum portfólio ainda. Crie o primeiro acima.
                </div>
            @endforelse
        </div>
    </div>
</div>
