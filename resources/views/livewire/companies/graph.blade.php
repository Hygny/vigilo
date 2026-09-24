<div class="py-8">
    <div class="mx-auto max-w-[1100px] space-y-5 px-5 sm:px-8">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <a href="{{ route('companies.show', $company) }}" wire:navigate class="mb-2 inline-flex items-center gap-1 text-[13px] font-medium text-ink-muted hover:text-ink-2">
                    <x-ui.icon name="arrow_back" :size="17" />Voltar à empresa
                </a>
                <h1 class="text-[26px] font-bold tracking-[-0.03em] text-ink">Grafo societário</h1>
                <p class="mt-1 text-[14px] text-ink-muted">
                    {{ $company->latestSnapshot?->razao_social ?? $company->label ?? $company->formattedCnpj() }}
                    @if ($available)
                        @if ($personMode)
                            <span class="text-ink-2">· {{ $groupCount }} empresa(s) desta pessoa</span>
                        @else
                            <span class="text-ink-2">· {{ $partnerCount }} sócio(s) · {{ $groupCount }} empresa(s) no grupo</span>
                        @endif
                    @endif
                </p>

                @if ($focused)
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1 rounded-full bg-primary-soft px-3 py-1 text-[12.5px] font-medium text-accent">
                            <x-ui.icon name="my_location" :size="14" />Focado em {{ $focusLabel ?? 'empresa conectada' }}
                        </span>
                        <button wire:click="resetFocus" class="inline-flex items-center gap-1 text-[12.5px] font-medium text-ink-muted hover:text-ink-2 cursor-pointer">
                            <x-ui.icon name="restart_alt" :size="15" />Voltar à empresa monitorada
                        </button>
                    </div>
                @endif
            </div>
        </div>

        @if (! $available)
            <div class="flex items-start gap-3 rounded-[14px] border border-line bg-surface-2 p-5 text-[14px] text-ink-2">
                <x-ui.icon name="warning" :size="20" class="mt-0.5 shrink-0 text-high" />
                <p>Grafo indisponível no momento — a base CNPJ não respondeu. Tente novamente em instantes.</p>
            </div>
        @else
            <x-ui.card class="p-3 sm:p-4">
                {{-- Canvas do grafo (Cytoscape). wire:ignore: o Livewire não mexe no
                     canvas; atualizações chegam pelo evento grifo-update. --}}
                <div wire:ignore x-data="grifo(@js($cyto))" @grifo-update.window="refresh($event.detail.graph)" class="relative">
                    <div x-ref="canvas" class="w-full rounded-[12px] bg-surface-2" style="height: 560px;" :style="{ height: expanded ? '80vh' : '560px' }"></div>

                    {{-- Controles de zoom / expandir (só o grafo) --}}
                    <div class="absolute right-3 top-3 flex flex-col overflow-hidden rounded-[10px] border border-line-strong bg-surface shadow-card">
                        <button type="button" @click="zoomIn()" title="Aproximar" class="flex h-9 w-9 items-center justify-center text-ink-2 hover:bg-surface-2 cursor-pointer"><x-ui.icon name="add" :size="18" /></button>
                        <button type="button" @click="zoomOut()" title="Afastar" class="flex h-9 w-9 items-center justify-center border-t border-line text-ink-2 hover:bg-surface-2 cursor-pointer"><x-ui.icon name="remove" :size="18" /></button>
                        <button type="button" @click="fit()" title="Ajustar à tela" class="flex h-9 w-9 items-center justify-center border-t border-line text-ink-2 hover:bg-surface-2 cursor-pointer"><x-ui.icon name="fit_screen" :size="18" /></button>
                        <button type="button" @click="toggleExpand()" :title="expanded ? 'Reduzir' : 'Expandir'" class="flex h-9 w-9 items-center justify-center border-t border-line text-ink-2 hover:bg-surface-2 cursor-pointer">
                            <span x-show="!expanded"><x-ui.icon name="open_in_full" :size="18" /></span>
                            <span x-show="expanded" style="display:none"><x-ui.icon name="close_fullscreen" :size="18" /></span>
                        </button>
                    </div>
                </div>

                {{-- Legenda --}}
                <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 border-t border-line pt-3 text-[12.5px] text-ink-2">
                    <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-full" style="background: var(--graph-company);"></span>Empresa</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-full" style="background: var(--graph-person);"></span>Sócio (pessoa)</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-full" style="background: var(--graph-negative);"></span>Situação negativa</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-full ring-2 ring-offset-1" style="background: var(--graph-company); --tw-ring-color: var(--graph-center-ring);"></span>No centro</span>
                    <span class="inline-flex items-center gap-1.5"><span class="inline-block w-5 border-t-2 border-dashed" style="border-color: var(--graph-edge-probable);"></span>Ligação provável (mesmo CPF, nome difere)</span>
                </div>

                <p class="mt-3 text-[12.5px] text-ink-muted">
                    @if ($personMode)
                        Empresas em que esta pessoa aparece como sócia. Arraste para mover, use a roda/os botões para dar zoom, passe o mouse para ver o CNPJ, e clique numa empresa para ver o grafo dela.
                    @else
                        Arraste os nós, use a roda ou os botões de zoom, passe o mouse numa bolha para ver o CNPJ, e clique numa empresa, sócio PJ ou pessoa para expandir a partir dela. Ligações <span class="font-medium">tracejadas</span> casam só pelo CPF mascarado (o nome diverge) — trate como prováveis.
                    @endif
                </p>
            </x-ui.card>

            {{-- Beneficiários finais (estrutura) — só no grafo centrado em empresa --}}
            @if (! $personMode)
                <x-ui.card class="p-5">
                    <div class="mb-3 flex items-center gap-2">
                        <x-ui.icon name="account_tree" :size="20" class="text-accent" />
                        <h2 class="text-[15px] font-semibold text-ink">Beneficiários finais (estrutura)</h2>
                    </div>

                    @if (count($beneficiaries) > 0)
                        <ul class="divide-y divide-line overflow-hidden rounded-btn ring-1 ring-line">
                            @foreach ($beneficiaries as $owner)
                                <li class="flex flex-wrap items-center justify-between gap-2 px-4 py-2.5">
                                    <div class="flex items-center gap-2">
                                        <x-ui.icon name="person" :size="16" class="text-ink-muted" />
                                        <span class="text-sm font-medium text-ink">{{ $owner->name }}</span>
                                        @if ($owner->type === 'ext')<span class="text-[11px] text-ink-muted">(estrangeiro)</span>@endif
                                    </div>
                                    <div class="flex items-center gap-3 text-[12.5px] text-ink-muted">
                                        @if ($owner->document)<span class="font-mono">{{ $owner->document }}</span>@endif
                                        <span>{{ $owner->depth === 1 ? 'sócio direto' : 'nível '.$owner->depth }}</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <p class="mt-3 text-[12px] text-ink-muted">
                            Estrutural (sem % de participação): pessoas físicas no topo da cadeia societária. Não aplica o critério legal de ≥25%.
                        </p>
                    @else
                        <p class="text-sm text-ink-muted">Nenhuma pessoa física identificada na cadeia dentro da profundidade analisada (pode haver sócios PJ além do limite).</p>
                    @endif
                </x-ui.card>
            @endif
        @endif
    </div>
</div>
