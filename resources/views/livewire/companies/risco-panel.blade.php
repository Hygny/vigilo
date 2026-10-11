<div>
    <x-ui.card class="p-[22px]">
        <div class="mb-4 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <x-ui.icon name="gavel" :size="20" class="text-accent" />
                <h2 class="text-[15px] font-semibold text-ink">Risco · Sanções (CGU)</h2>
            </div>
            @if ($report->configurado && $report->disponivel)
                @if ($report->temSancoes())
                    <x-ui.badge tone="crit" icon="gpp_bad">{{ $report->total() }} {{ $report->total() === 1 ? 'sanção' : 'sanções' }}</x-ui.badge>
                @else
                    <x-ui.badge tone="ok" icon="verified_user">Nada consta</x-ui.badge>
                @endif
            @endif
        </div>

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
                        @if ($sancao->orgao)
                            <p class="mt-1.5 text-[13.5px] text-ink-2"><span class="text-ink-muted">Órgão:</span> {{ $sancao->orgao }}</p>
                        @endif
                        @if ($sancao->fundamentacao)
                            <p class="mt-0.5 text-[13px] text-ink-muted">{{ $sancao->fundamentacao }}</p>
                        @endif
                    </li>
                @endforeach
            </ul>
            <p class="mt-3 text-xs text-ink-muted">Fonte: Portal da Transparência (CGU) — cadastros CEIS, CNEP e CEPIM. A sanção foi vinculada a este CNPJ.</p>
        @endif
    </x-ui.card>
</div>
