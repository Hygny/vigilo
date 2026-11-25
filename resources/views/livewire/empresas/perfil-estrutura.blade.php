<div class="space-y-5">
    {{-- Beneficiários finais (estrutura) --}}
    <x-ui.card class="p-[22px]">
        <div class="mb-3 flex items-center gap-2"><x-ui.icon name="account_tree" :size="20" class="text-accent" /><h2 class="text-[15px] font-semibold text-ink">Beneficiários finais (estrutura)</h2></div>
        @if ($beneficiarios === null)
            <p class="text-sm text-ink-muted">Indisponível no momento.</p>
        @elseif ($beneficiarios === [])
            <p class="text-sm text-ink-muted">Nenhuma pessoa física identificada na cadeia societária dentro da profundidade analisada.</p>
        @else
            <ul class="divide-y divide-line">
                @foreach ($beneficiarios as $owner)
                    <li class="flex flex-wrap items-center justify-between gap-2 py-2.5 text-sm">
                        <span class="font-medium text-ink">{{ $owner->name }} @if ($owner->document)<span class="font-mono text-[12.5px] text-ink-muted">({{ $owner->document }})</span>@endif @if ($owner->type === 'ext')<span class="text-[11px] text-ink-muted">· estrangeiro</span>@endif</span>
                        <span class="text-ink-muted">{{ $owner->depth === 1 ? 'sócio direto' : 'nível '.$owner->depth.' da cadeia' }}</span>
                    </li>
                @endforeach
            </ul>
            <p class="mt-3 text-[12px] text-ink-muted">Pessoas no topo da cadeia societária (sobe pelos sócios PJ). Estrutural: a base aberta não traz % de participação, então não aplica o critério legal de 25%.</p>
        @endif
    </x-ui.card>

    {{-- Participações (empresas em que esta é sócia PJ) --}}
    <x-ui.card class="p-[22px]">
        <div class="mb-3 flex items-center gap-2"><x-ui.icon name="domain_add" :size="20" class="text-accent" /><h2 class="text-[15px] font-semibold text-ink">Participações</h2>
            @if ($participacoes)<span class="text-[12.5px] text-ink-muted">· sócia de {{ $totalParticipacoes }} empresa(s)</span>@endif
        </div>
        @if ($participacoes === null)
            <p class="text-sm text-ink-muted">Indisponível no momento.</p>
        @elseif ($participacoes === [])
            <p class="text-sm text-ink-muted">Esta empresa não aparece como sócia de outras empresas.</p>
        @else
            <ul class="divide-y divide-line">
                @foreach ($participacoes as $p)
                    <li class="flex flex-wrap items-center justify-between gap-2 py-2.5 text-sm">
                        <span class="flex min-w-0 items-center gap-2">
                            <a href="{{ route('empresas.perfil', $p->cnpj) }}" wire:navigate class="truncate font-medium text-ink hover:underline">{{ $p->nome }}</a>
                            @if ($p->situacao && $p->situacao !== 'ATIVA')<x-ui.badge tone="crit">{{ $p->situacao }}</x-ui.badge>@endif
                        </span>
                        <span class="text-ink-muted">{{ collect([$p->qualificacao, $p->dataEntrada ? 'desde '.\Illuminate\Support\Carbon::parse($p->dataEntrada)->format('d/m/Y') : null, $p->uf])->filter()->implode(' · ') }}</span>
                    </li>
                @endforeach
            </ul>
            @if ($totalParticipacoes > count($participacoes))
                <p class="mt-3 text-[12px] text-ink-muted">Mostrando {{ count($participacoes) }} de {{ $totalParticipacoes }} (ativas primeiro).</p>
            @endif
        @endif
    </x-ui.card>

    {{-- Filiais e demais estabelecimentos --}}
    <x-ui.card class="p-[22px]">
        <div class="mb-3 flex items-center gap-2"><x-ui.icon name="store" :size="20" class="text-accent" /><h2 class="text-[15px] font-semibold text-ink">Estabelecimentos</h2>
            @if ($estabelecimentos > 0)<span class="text-[12.5px] text-ink-muted">· {{ $estabelecimentos }} no total (matriz + filiais)</span>@endif
        </div>
        @if ($filiais === null)
            <p class="text-sm text-ink-muted">Indisponível no momento.</p>
        @elseif ($filiais === [])
            <p class="text-sm text-ink-muted">Único estabelecimento — a empresa não tem filiais.</p>
        @else
            <ul class="divide-y divide-line">
                @foreach ($filiais as $filial)
                    <li class="flex flex-wrap items-center justify-between gap-2 py-2.5 text-sm">
                        <span class="flex min-w-0 items-center gap-2">
                            <a href="{{ route('empresas.perfil', $filial->cnpj) }}" wire:navigate class="truncate font-medium text-ink hover:underline">{{ $filial->nomeFantasia ?? ($filial->isMatriz ? 'Matriz' : 'Filial') }}</a>
                            @if ($filial->isMatriz)<span class="shrink-0 rounded-md bg-primary-soft px-1.5 py-0.5 text-[11px] font-semibold text-accent">matriz</span>@endif
                            @if ($filial->situacao && $filial->situacao !== 'ATIVA')<x-ui.badge tone="crit">{{ $filial->situacao }}</x-ui.badge>@endif
                        </span>
                        <span class="flex items-center gap-3 text-ink-muted">
                            @if ($filial->municipio)<span>{{ $filial->municipio }}{{ $filial->uf ? '/'.$filial->uf : '' }}</span>@endif
                            <span class="font-mono text-[12.5px]">{{ \App\Support\Cnpj::tryFrom($filial->cnpj)?->formatted() ?? $filial->cnpj }}</span>
                        </span>
                    </li>
                @endforeach
            </ul>
            @if ($estabelecimentos - 1 > count($filiais))
                <p class="mt-3 text-[12px] text-ink-muted">Mostrando os {{ count($filiais) }} primeiros de {{ $estabelecimentos - 1 }} outros estabelecimentos.</p>
            @endif
        @endif
    </x-ui.card>
</div>
