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
                        <span class="text-ink-2">· {{ $partnerCount }} sócio(s) · {{ $groupCount }} empresa(s) no grupo</span>
                    @endif
                </p>
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
                            @php $stroke = $node['kind'] === 'empresa' ? 'var(--line-strong)' : 'var(--surface)'; @endphp
                            <g>
                                <title>{{ $node['title'] }}</title>
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
                    Camada 1: sócios diretos e empresas ligadas por sócio em comum. Níveis mais profundos e o beneficiário final chegam numa próxima etapa.
                </p>
            </x-ui.card>
        @endif
    </div>
</div>
