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
                            @if ($showAddress)
                                <span class="text-ink-2">· {{ $addressCount }} no mesmo endereço</span>
                            @endif
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

                {{-- Conexões por sócio PF: off por padrão (muito falso positivo
                     pelo CPF mascarado). Só no modo empresa. --}}
                @if (! $personMode)
                    <div class="mt-3 flex flex-wrap items-center gap-2 border-t border-line pt-3">
                        <button type="button" wire:click="toggleProbable"
                                @class([
                                    'inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-[12.5px] font-medium transition-colors cursor-pointer',
                                    'border-primary bg-primary-soft text-accent' => $showProbable,
                                    'border-line-strong bg-surface text-ink-2 hover:bg-surface-2' => ! $showProbable,
                                ])>
                            <x-ui.icon :name="$showProbable ? 'visibility' : 'visibility_off'" :size="15" />
                            {{ $showProbable ? 'Ocultar conexões por sócio PF' : 'Mostrar conexões por sócio PF' }}
                        </button>
                        <button type="button" wire:click="toggleAddress"
                                @class([
                                    'inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-[12.5px] font-medium transition-colors cursor-pointer',
                                    'border-primary bg-primary-soft text-accent' => $showAddress,
                                    'border-line-strong bg-surface text-ink-2 hover:bg-surface-2' => ! $showAddress,
                                ])>
                            <x-ui.icon name="location_on" :size="15" />
                            {{ $showAddress ? 'Ocultar empresas no mesmo endereço' : 'Mostrar empresas no mesmo endereço' }}
                        </button>
                    </div>
                    <p class="mt-2 text-[12px] text-ink-muted">
                        <span class="font-medium">Sócio PF</span>: ligações por CPF mascarado (menor certeza). <span class="font-medium">Mesmo endereço</span>: empresas no mesmo CEP + número. Ambas ocultas por padrão.
                    </p>
                @endif

                {{-- Legenda --}}
                <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 {{ $personMode ? 'border-t border-line pt-3' : '' }} text-[12.5px] text-ink-2">
                    <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-full" style="background: var(--graph-company);"></span>Empresa</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-full" style="background: var(--graph-person);"></span>Sócio (pessoa)</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-full" style="background: var(--graph-negative);"></span>Situação negativa</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-full ring-2 ring-offset-1" style="background: var(--graph-company); --tw-ring-color: var(--graph-center-ring);"></span>No centro</span>
                    @if (! $personMode && $showProbable)
                        <span class="inline-flex items-center gap-1.5"><span class="inline-block w-5 border-t-2 border-dashed" style="border-color: var(--graph-edge-probable);"></span>Conexão por sócio PF (CPF mascarado)</span>
                    @endif
                    @if (! $personMode && $showAddress)
                        <span class="inline-flex items-center gap-1.5"><span class="inline-block w-5 border-t-2 border-dotted" style="border-color: var(--graph-edge-address);"></span>Mesmo endereço (CEP + número)</span>
                    @endif
                </div>

                <p class="mt-3 text-[12.5px] text-ink-muted">
                    @if ($personMode)
                        Empresas em que esta pessoa aparece como sócia. Arraste para mover, use a roda/os botões para dar zoom, passe o mouse para ver o CNPJ, e clique numa empresa para ver o grafo dela.
                    @else
                        O grupo econômico mostra por padrão só as ligações <span class="font-medium">certas</span> — sócios diretos e empresas ligadas por <span class="font-medium">sócio PJ</span> (CNPJ completo). As <span class="font-medium">filiais</span> (mesma empresa) estão no painel abaixo. Arraste os nós, use a roda/os botões de zoom, passe o mouse numa bolha para ver o CNPJ, e clique para expandir. Use <span class="font-medium">"Mostrar conexões por sócio PF"</span> para ver também as ligações por pessoa física (CPF mascarado — menor certeza, tracejadas).
                    @endif
                </p>
            </x-ui.card>

            {{-- Filiais (mesma empresa, por CNPJ) — relação 100% certa. Só no modo empresa. --}}
            @if (! $personMode)
                <x-ui.card class="p-5">
                    <div class="mb-3 flex items-center gap-2">
                        <x-ui.icon name="store" :size="20" class="text-accent" />
                        <h2 class="text-[15px] font-semibold text-ink">Filiais</h2>
                        <span class="text-[12.5px] text-ink-muted">· mesma empresa (mesmo CNPJ base)</span>
                    </div>

                    @if (count($branches) > 0)
                        <ul class="divide-y divide-line overflow-hidden rounded-btn ring-1 ring-line">
                            @foreach ($branches as $branch)
                                <li class="flex flex-wrap items-center justify-between gap-2 px-4 py-2.5">
                                    <div class="flex min-w-0 items-center gap-2">
                                        <x-ui.icon :name="$branch->isMatriz ? 'home_work' : 'store'" :size="16" class="shrink-0 text-ink-muted" />
                                        <span class="truncate text-sm font-medium text-ink">{{ $branch->nomeFantasia ?? ($branch->isMatriz ? 'Matriz' : 'Filial') }}</span>
                                        @if ($branch->isMatriz)<span class="shrink-0 rounded-md bg-primary-soft px-1.5 py-0.5 text-[11px] font-semibold text-accent">matriz</span>@endif
                                        @if ($branch->situacao && $branch->situacao !== 'ATIVA')<span class="shrink-0 rounded-md px-1.5 py-0.5 text-[11px] font-semibold text-danger ring-1 ring-inset ring-line">{{ $branch->situacao }}</span>@endif
                                    </div>
                                    <div class="flex items-center gap-3 text-[12.5px] text-ink-muted">
                                        @if ($branch->municipio)<span>{{ $branch->municipio }}{{ $branch->uf ? '/'.$branch->uf : '' }}</span>@endif
                                        <span class="font-mono">{{ \App\Support\Cnpj::tryFrom($branch->cnpj)?->formatted() ?? $branch->cnpj }}</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <p class="mt-3 text-[12px] text-ink-muted">
                            Estabelecimentos com o mesmo CNPJ base (matriz + filiais) — relação certa pelo CNPJ completo.
                        </p>
                    @else
                        <p class="text-sm text-ink-muted">Sem filiais — a empresa tem um único estabelecimento.</p>
                    @endif
                </x-ui.card>
            @endif

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
