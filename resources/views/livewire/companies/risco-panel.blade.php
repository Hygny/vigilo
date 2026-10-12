<div>
    <x-ui.card class="p-[22px]">
        <div class="mb-1 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <x-ui.icon name="gavel" :size="20" class="text-accent" />
                <h2 class="text-[15px] font-semibold text-ink">Risco</h2>
            </div>
            @if ($nivel)
                <x-ui.badge :tone="$nivel->tone()" :icon="$nivel->icon()">Risco {{ $nivel->label() }}</x-ui.badge>
            @endif
        </div>
        @if ($motivo)
            <p class="mb-4 text-[13px] text-ink-muted">{{ $motivo }}</p>
        @endif

        {{-- Lista Suja do trabalho escravo (MTE): sinal crítico, sempre no topo --}}
        @if ($listaSuja)
            <div class="mb-4 flex items-start gap-2 rounded-btn bg-crit-soft px-4 py-3 text-sm text-crit">
                <x-ui.icon name="gavel" :size="18" class="mt-0.5" />
                <div>
                    <p class="font-semibold">Consta na Lista Suja do trabalho escravo (MTE).</p>
                    @php($detalhe = collect([$listaSuja->nome, $listaSuja->uf, $listaSuja->ano ? 'ano '.$listaSuja->ano : null])->filter()->implode(' · '))
                    @if ($detalhe)<p class="mt-0.5 text-xs">{{ $detalhe }}</p>@endif
                </div>
            </div>
        @endif

        {{-- Sanções (CGU) --}}
        <h3 class="mb-2 text-[11px] font-semibold uppercase tracking-wide text-ink-muted">Sanções (CGU)</h3>

        @if (! $report->configurado)
            <div class="flex items-start gap-2 text-sm text-ink-muted">
                <x-ui.icon name="info" :size="18" class="mt-0.5" />
                <p>Consulta de sanções indisponível: a integração com o Portal da Transparência ainda não foi configurada.</p>
            </div>
        @elseif (! $report->disponivel)
            <div class="flex items-start gap-2 text-sm text-ink-muted">
                <x-ui.icon name="cloud_off" :size="18" class="mt-0.5" />
                <p>Não foi possível consultar as sanções agora. Tente novamente mais tarde.</p>
            </div>
        @elseif (! $report->temSancoes())
            <div class="flex items-start gap-2 rounded-btn bg-ok-soft px-4 py-3 text-sm text-ok">
                <x-ui.icon name="verified_user" :size="18" class="mt-0.5" />
                <p>Nenhuma sanção encontrada nos cadastros <strong>CEIS</strong>, <strong>CNEP</strong> e <strong>CEPIM</strong> para este CNPJ.</p>
            </div>
        @else
            <ul class="space-y-3">
                @foreach ($report->sancoes as $sancao)
                    <li class="rounded-btn p-4 ring-1 ring-line">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="inline-flex items-center gap-2">
                                <x-ui.badge tone="crit">{{ $sancao->fonte->sigla() }}</x-ui.badge>
                                <span class="text-sm font-semibold text-ink">{{ $sancao->tipo ?? 'Sanção registrada' }}</span>
                            </span>
                            @if ($sancao->dataInicio || $sancao->dataFim)
                                <span class="font-mono text-xs text-ink-muted">
                                    {{ $sancao->dataInicio ? \Illuminate\Support\Carbon::parse($sancao->dataInicio)->format('d/m/Y') : '—' }}@if ($sancao->dataFim) → {{ \Illuminate\Support\Carbon::parse($sancao->dataFim)->format('d/m/Y') }}@endif
                                </span>
                            @endif
                        </div>

                        {{-- O que significa, em linguagem simples (sem juridiquês) --}}
                        <p class="mt-1.5 text-[13.5px] text-ink-2">{{ $sancao->fonte->explicacao() }}</p>

                        @if ($sancao->orgao)
                            <p class="mt-1 text-xs text-ink-muted">Aplicada por: {{ $sancao->orgao }}</p>
                        @endif

                        @if ($sancao->fundamentacao)
                            <details class="mt-2">
                                <summary class="cursor-pointer select-none text-xs font-medium text-ink-muted hover:text-ink-2">Base legal</summary>
                                <p class="mt-1 text-xs leading-relaxed text-ink-muted">{{ $sancao->fundamentacao }}</p>
                            </details>
                        @endif
                    </li>
                @endforeach
            </ul>
            <p class="mt-3 text-xs text-ink-muted">Fonte: Portal da Transparência (CGU) — dados públicos sujeitos a atualização; confirme na fonte oficial.</p>
        @endif
    </x-ui.card>
</div>
