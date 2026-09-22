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
            <x-ui.card class="p-3 sm:p-5">
                <div class="overflow-x-auto">
                    <svg viewBox="0 0 {{ $layout['width'] }} {{ $layout['height'] }}" class="mx-auto block h-auto w-full" style="max-width:{{ $layout['width'] }}px;" role="img" aria-label="Grafo societário">
                        @foreach ($layout['edges'] as $edge)
                            <line x1="{{ $edge['x1'] }}" y1="{{ $edge['y1'] }}" x2="{{ $edge['x2'] }}" y2="{{ $edge['y2'] }}"
                                  style="stroke: var(--line-strong); stroke-width: 1.5px;" />
                        @endforeach

                        @foreach ($layout['nodes'] as $node)
                            @php
                                $stroke = $node['kind'] === 'empresa' ? 'var(--line-strong)' : 'var(--surface)';
                                // cnpj (14 díg) e person (dígitos + `*`) já são validados no
                                // componente → injeção segura no wire:click.
                                if ($node['cnpj']) {
                                    $clickAttrs = 'wire:click="focusOn(\''.$node['cnpj'].'\')" style="cursor:pointer;"';
                                } elseif ($node['person']) {
                                    $clickAttrs = 'wire:click="focusPerson(\''.$node['person'].'\')" style="cursor:pointer;"';
                                } else {
                                    $clickAttrs = '';
                                }
                                $titleSuffix = ($node['cnpj'] || $node['person']) ? ' — clique para expandir' : '';
                            @endphp
                            <g {!! $clickAttrs !!}>
                                <title>{{ $node['title'].$titleSuffix }}</title>
                                <circle cx="{{ $node['x'] }}" cy="{{ $node['y'] }}" r="{{ $node['r'] }}"
                                        style="fill: {{ $node['fill'] }}; stroke: {{ $stroke }}; stroke-width: 2.5px;" />
                                <text x="{{ $node['x'] }}" y="{{ $node['y'] + $node['r'] + 14 }}" text-anchor="middle"
                                      style="fill: var(--ink); font-size: 11px; font-weight: 500;">{{ $node['label'] }}</text>
                            </g>
                        @endforeach
                    </svg>
                </div>

                {{-- Legenda --}}
                <div class="mt-2 flex flex-wrap items-center gap-x-5 gap-y-2 border-t border-line pt-3 text-[12.5px] text-ink-2">
                    <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-full" style="background: var(--primary);"></span>Empresa consultada</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-full" style="background: var(--high);"></span>Sócio</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-full border border-line-strong" style="background: var(--surface-3);"></span>Empresa do grupo (sócio em comum)</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-full" style="background: var(--crit);"></span>Situação negativa</span>
                </div>

                <p class="mt-3 text-[12.5px] text-ink-muted">
                    @if ($personMode)
                        Empresas em que esta pessoa aparece como sócia. Clique numa empresa para ver o grafo dela.
                    @else
                        Camada 1: sócios diretos e empresas ligadas por sócio em comum. Clique numa empresa, sócio PJ ou pessoa para expandir a partir dela.
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
