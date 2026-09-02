<div class="py-8">
    <div class="mx-auto max-w-[1000px] space-y-5 px-5 sm:px-8">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <a href="{{ route('portfolios.show', $company->portfolio_id) }}" wire:navigate class="mb-2 inline-flex items-center gap-1 text-[13px] font-medium text-ink-muted hover:text-ink-2">
                    <x-ui.icon name="arrow_back" :size="17" />Voltar ao portfólio
                </a>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-[26px] font-bold tracking-[-0.03em] text-ink">{{ $latest?->razao_social ?? $company->label ?? 'Empresa monitorada' }}</h1>
                    @if ($company->label !== null && $latest?->razao_social !== null)
                        <span class="inline-flex items-center gap-1 rounded-md bg-surface-2 px-2 py-0.5 text-sm font-medium text-ink-2 ring-1 ring-inset ring-line" title="Seu rótulo para esta unidade">
                            <x-ui.icon name="sell" :size="14" class="text-ink-muted" />{{ $company->label }}
                        </span>
                    @endif
                </div>
                <p class="mt-1 font-mono text-sm text-ink-muted">{{ $company->formattedCnpj() }}</p>
            </div>
            <x-ui.button wire:click="queueRefresh" icon="bolt">Atualizar agora</x-ui.button>
        </div>

        @if (session('status'))
            <div class="flex items-center gap-2 rounded-btn border border-line bg-surface px-4 py-3 text-sm text-ink-2 shadow-card">
                <x-ui.icon name="check_circle" :size="18" class="text-ok" />{{ session('status') }}
            </div>
        @endif

        {{-- Dados atuais --}}
        <x-ui.card class="p-[22px]">
            <div class="mb-4 flex items-center gap-2"><x-ui.icon name="description" :size="20" class="text-accent" /><h2 class="text-[15px] font-semibold text-ink">Dados atuais</h2></div>
            @if ($latest === null)
                <p class="text-sm text-ink-muted">Ainda não há dados. Clique em “Atualizar agora” para buscar na Receita.</p>
            @else
                <dl class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                    <div><dt class="text-[11px] font-semibold uppercase tracking-wide text-ink-muted">Situação cadastral</dt><dd class="mt-0.5 text-sm text-ink-2">{{ $latest->situacao_cadastral }} @if ($latest->situacao_data)<span class="text-ink-muted">({{ $latest->situacao_data->format('d/m/Y') }})</span>@endif</dd></div>
                    <div><dt class="text-[11px] font-semibold uppercase tracking-wide text-ink-muted">Nome fantasia</dt><dd class="mt-0.5 text-sm text-ink-2">{{ $latest->nome_fantasia ?? '—' }}</dd></div>
                    <div><dt class="text-[11px] font-semibold uppercase tracking-wide text-ink-muted">CNAE principal</dt><dd class="mt-0.5 text-sm text-ink-2">{{ $latest->cnae_principal ?? '—' }}</dd></div>
                    <div><dt class="text-[11px] font-semibold uppercase tracking-wide text-ink-muted">Porte</dt><dd class="mt-0.5 text-sm text-ink-2">{{ $latest->porte ?? '—' }}</dd></div>
                    <div><dt class="text-[11px] font-semibold uppercase tracking-wide text-ink-muted">Natureza jurídica</dt><dd class="mt-0.5 text-sm text-ink-2">{{ $latest->natureza_juridica ?? '—' }}</dd></div>
                    <div><dt class="text-[11px] font-semibold uppercase tracking-wide text-ink-muted">Endereço</dt><dd class="mt-0.5 text-sm text-ink-2">{{ collect([$latest->logradouro, $latest->municipio, $latest->uf])->filter()->implode(' · ') ?: '—' }}</dd></div>
                </dl>

                @if ($latest->partners->isNotEmpty())
                    <div class="mt-5">
                        <h3 class="mb-2 text-[11px] font-semibold uppercase tracking-wide text-ink-muted">Quadro societário</h3>
                        <ul class="divide-y divide-line overflow-hidden rounded-btn ring-1 ring-line">
                            @foreach ($latest->partners as $partner)
                                <li class="flex items-center justify-between px-4 py-2.5 text-sm">
                                    <span class="text-ink-2">{{ $partner->nome }}</span>
                                    <span class="text-ink-muted">{{ $partner->qualificacao ?? '' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @endif
        </x-ui.card>

        {{-- Timeline de mudanças --}}
        <x-ui.card>
            <div class="flex items-center gap-2 border-b border-line px-[22px] py-4"><x-ui.icon name="history" :size="20" class="text-accent" /><h2 class="text-[15px] font-semibold text-ink">Histórico de mudanças</h2></div>
            @forelse ($events as $event)
                <div class="flex items-start justify-between gap-4 border-b border-line px-[22px] py-3.5 last:border-b-0">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-ink">{{ $event->type->label() }}</p>
                        <p class="text-[13.5px] text-ink-2"><span class="text-ink-muted line-through">{{ $event->old_value ?? '—' }}</span> <x-ui.icon name="arrow_right_alt" :size="15" class="align-middle text-ink-muted" /> <span class="font-medium text-ink">{{ $event->new_value ?? '—' }}</span></p>
                    </div>
                    <div class="flex shrink-0 flex-col items-end gap-1">
                        <x-ui.badge :tone="$event->severity->tone()" dot>{{ $event->severity->label() }}</x-ui.badge>
                        <span class="font-mono text-xs text-ink-muted">{{ $event->detected_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            @empty
                <div class="px-[22px] py-10 text-center text-sm text-ink-muted">Nenhuma mudança registrada. A primeira coleta serve de linha de base.</div>
            @endforelse
        </x-ui.card>

        {{-- Coletas --}}
        <x-ui.card>
            <div class="border-b border-line px-[22px] py-4"><h2 class="text-[15px] font-semibold text-ink">Coletas ({{ $snapshots->count() }})</h2></div>
            @foreach ($snapshots as $snapshot)
                <div class="flex items-center justify-between border-b border-line px-[22px] py-2.5 text-sm last:border-b-0">
                    <span class="text-ink-2">{{ $snapshot->situacao_cadastral }} — {{ $snapshot->razao_social }}</span>
                    <span class="font-mono text-xs text-ink-muted">{{ $snapshot->captured_at->format('d/m/Y H:i') }}</span>
                </div>
            @endforeach
        </x-ui.card>
    </div>
</div>
