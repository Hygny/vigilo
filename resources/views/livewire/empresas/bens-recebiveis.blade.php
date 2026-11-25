{{-- Bens e recebíveis: onde procurar penhora (recuperação de crédito). Espera $bens (BensReport). --}}
<div>
    <x-ui.card class="p-[22px]">
        <div class="mb-3 flex items-center gap-2">
            <x-ui.icon name="account_balance_wallet" :size="20" class="text-accent" />
            <h2 class="text-[15px] font-semibold text-ink">Bens e recebíveis</h2>
            <span class="text-[12.5px] text-ink-muted">· onde procurar penhora</span>
        </div>

        {{-- Resumo --}}
        <div class="mb-4 flex flex-wrap gap-2 text-[12.5px]">
            @if ($bens->aeronaves !== null)
                <span class="rounded-full bg-surface-2 px-3 py-1 text-ink-2 ring-1 ring-inset ring-line">{{ $bens->totalAeronavesAtivas }} aeronave(s) · {{ $bens->aeronavesTruncadas() ? 'ao menos ' : '' }}{{ count($bens->aeronavesLivres()) }} sem ônus</span>
            @endif
            @if ($bens->participacoes !== null)
                <span class="rounded-full bg-surface-2 px-3 py-1 text-ink-2 ring-1 ring-inset ring-line">sócia de {{ $bens->totalParticipacoes }} empresa(s) · quotas/ações</span>
            @endif
            @if ($bens->contratos->disponivel)
                <span class="rounded-full bg-surface-2 px-3 py-1 text-ink-2 ring-1 ring-inset ring-line">{{ $bens->contratos->truncado ? 'ao menos ' : '' }}{{ count($bens->contratosVigentes()) }} contrato(s) federal(is) vigente(s) · {{ 'R$ '.number_format($bens->valorContratosVigentes(), 2, ',', '.') }}</span>
            @endif
        </div>

        {{-- Aeronaves (RAB/ANAC) --}}
        <h3 class="mb-2 text-[11px] font-semibold uppercase tracking-wide text-ink-muted">Aeronaves (Registro Aeronáutico Brasileiro)</h3>
        @if ($bens->aeronaves === null)
            <p class="text-sm text-ink-muted">Cópia do registro de aeronaves indisponível no momento — não consultado.</p>
        @elseif ($bens->aeronaves === [])
            <p class="text-sm text-ink-muted">Nenhuma aeronave registrada em nome desta empresa (como proprietária ou operadora).</p>
        @else
            <ul class="divide-y divide-line">
                @foreach ($bens->aeronaves as $a)
                    <li class="flex flex-wrap items-center justify-between gap-2 py-2.5 text-sm">
                        <span class="flex min-w-0 items-center gap-2">
                            <span class="font-mono font-semibold text-ink">{{ $a->marca }}</span>
                            <span class="truncate text-ink-2">{{ $a->descricao() }}</span>
                            @if ($a->cancelada())<span class="rounded-md bg-surface-2 px-1.5 py-0.5 text-[11px] text-ink-muted ring-1 ring-inset ring-line">matrícula cancelada</span>@elseif ($a->reservada())<span class="rounded-md bg-surface-2 px-1.5 py-0.5 text-[11px] text-ink-muted ring-1 ring-inset ring-line">marca reservada · não matriculada</span>@endif
                        </span>
                        <span class="flex flex-wrap items-center gap-2 text-[12.5px] text-ink-muted">
                            {{ $a->rotuloPapeis(empresa: true) }}@if ($a->percentual !== null) ({{ number_format($a->percentual, 0, ',', '.') }}%)@endif
                            @if ($a->onus())
                                <span class="rounded-md bg-med-soft px-1.5 py-0.5 text-[11px] font-semibold text-med">{{ $a->onus() }}</span>
                            @elseif ($a->livre())
                                <span class="rounded-md bg-ok-soft px-1.5 py-0.5 text-[11px] font-semibold text-ok">sem ônus</span>
                            @endif
                        </span>
                    </li>
                @endforeach
            </ul>
            @if ($bens->aeronavesTruncadas())
                <p class="mt-2 text-[12px] text-ink-muted">Mostrando {{ count($bens->aeronaves) }} de {{ $bens->totalAeronaves }} (ativas primeiro).</p>
            @endif
            <p class="mt-2 text-[12px] text-ink-muted">Fonte: ANAC (RAB, dados abertos){{ $bens->aeronavesDe ? ' — cópia de '.\Illuminate\Support\Carbon::parse($bens->aeronavesDe)->format('d/m/Y') : '' }}. Confirme na certidão de propriedade e ônus reais da ANAC antes de pedir penhora.</p>
        @endif

        {{-- Participações (quotas/ações penhoráveis) --}}
        <h3 class="mb-2 mt-5 text-[11px] font-semibold uppercase tracking-wide text-ink-muted">Participações societárias (quotas/ações)</h3>
        @if ($bens->participacoes === null)
            <p class="text-sm text-ink-muted">Indisponível no momento.</p>
        @elseif ($bens->participacoes === [])
            <p class="text-sm text-ink-muted">Não aparece como sócia de outras empresas.</p>
        @else
            <ul class="divide-y divide-line">
                @foreach ($bens->participacoes as $p)
                    <li class="flex flex-wrap items-center justify-between gap-2 py-2 text-sm">
                        <a href="{{ route('empresas.perfil', $p->cnpj) }}" wire:navigate class="truncate font-medium text-ink hover:underline">{{ $p->nome }}</a>
                        <span class="text-[12.5px] {{ $p->situacao && $p->situacao !== 'ATIVA' ? 'text-danger' : 'text-ink-muted' }}">{{ $p->situacao ?? '—' }}</span>
                    </li>
                @endforeach
            </ul>
            @if ($bens->totalParticipacoes > count($bens->participacoes))
                <p class="mt-2 text-[12px] text-ink-muted">Mostrando {{ count($bens->participacoes) }} de {{ $bens->totalParticipacoes }} (ativas primeiro) — a lista completa está na estrutura societária.</p>
            @endif
        @endif

        {{-- Recebíveis: contratos públicos vigentes --}}
        <h3 class="mb-2 mt-5 text-[11px] font-semibold uppercase tracking-wide text-ink-muted">Créditos a receber do governo federal</h3>
        @if (! $bens->contratos->configurado || ! $bens->contratos->disponivel)
            <p class="text-sm text-ink-muted">Contratos públicos não consultados agora.</p>
        @elseif ($bens->contratosVigentes() === [])
            <p class="text-sm text-ink-muted">Nenhum contrato federal vigente como fornecedora.</p>
        @else
            <p class="text-sm text-ink-2">{{ $bens->contratos->truncado ? 'Ao menos ' : '' }}{{ count($bens->contratosVigentes()) }} contrato(s) vigente(s), somando {{ 'R$ '.number_format($bens->valorContratosVigentes(), 2, ',', '.') }} (valor contratado). Pagamentos a receber desses contratos podem ser penhorados — detalhes no card de contratos públicos.</p>
        @endif

        <p class="mt-4 text-[12px] text-ink-muted">Imóveis, veículos e contas bancárias não têm fonte aberta (cartórios/ONR, Detran, RENAJUD e SISBAJUD são pagos ou só por ordem judicial).</p>
    </x-ui.card>
</div>
