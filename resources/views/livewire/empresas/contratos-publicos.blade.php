<div>
    <x-ui.card class="p-[22px]">
        <div class="mb-1 flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <x-ui.icon name="contract" :size="20" class="text-accent" />
                <h2 class="text-[15px] font-semibold text-ink">Contratos públicos</h2>
                <span class="text-[12.5px] text-ink-muted">· como fornecedora do governo federal</span>
            </div>
            @if ($report->disponivel && $report->total() > 0)
                <span class="text-[12.5px] text-ink-2">{{ $report->truncado ? 'ao menos ' : '' }}{{ $report->total() }} contrato(s) · {{ 'R$ '.number_format($report->valorTotal(), 2, ',', '.') }} · {{ $report->vigentes() }} vigente(s)</span>
            @endif
        </div>

        @if (! $report->configurado)
            <p class="mt-2 text-sm text-ink-muted">Consulta indisponível: a integração com o Portal da Transparência ainda não foi configurada.</p>
        @elseif (! $report->disponivel)
            <div class="mt-2 flex items-start gap-2 text-sm text-ink-muted">
                <x-ui.icon name="cloud_off" :size="18" class="mt-0.5" />
                <p>Não foi possível consultar os contratos agora. Tente novamente mais tarde.</p>
            </div>
        @elseif ($report->contratos === [])
            <p class="mt-2 text-sm text-ink-muted">Nenhum contrato do Poder Executivo Federal com esta empresa como fornecedora.</p>
        @else
            <ul class="mt-3 divide-y divide-line">
                @foreach (array_slice($report->contratos, 0, $mostrar) as $c)
                    <li class="py-3 text-sm">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <p class="min-w-0 flex-1 font-medium text-ink">{{ \Illuminate\Support\Str::limit($c->objeto ?? 'Contrato sem objeto informado', 160, preserveWords: true) }}</p>
                            @if ($c->valor() !== null)
                                <span class="shrink-0 font-mono text-[12.5px] text-ink-2">{{ 'R$ '.number_format($c->valor(), 2, ',', '.') }}</span>
                            @endif
                        </div>
                        <p class="mt-1 text-[12.5px] text-ink-muted">
                            {{ $c->detalhe() }}
                            @if ($c->vigente())<span class="ml-1 rounded-md bg-ok-soft px-1.5 py-0.5 text-[11px] font-semibold text-ok">vigente</span>@endif
                            @if ($c->url())<a href="{{ $c->url() }}" target="_blank" rel="noopener noreferrer" class="ml-1 font-medium text-ink-2 underline hover:text-ink">ver no Portal</a>@endif
                        </p>
                    </li>
                @endforeach
            </ul>
            @if ($report->truncado)
                <p class="mt-2 text-[12px] text-ink-muted">Mostrando {{ min($mostrar, $report->total()) }} de {{ $report->total() }} lidos. A consulta parou no teto de páginas e a API não ordena por data: pode haver mais contratos, inclusive mais recentes — veja a lista completa na <a href="https://portaldatransparencia.gov.br/pessoa-juridica/{{ $cnpj }}" target="_blank" rel="noopener noreferrer" class="font-medium text-ink-2 underline hover:text-ink">página da empresa no Portal da Transparência</a>.</p>
            @elseif ($report->total() > $mostrar)
                <p class="mt-2 text-[12px] text-ink-muted">Mostrando os {{ $mostrar }} mais recentes de {{ $report->total() }}.</p>
            @endif
        @endif

        <p class="mt-3 text-[12px] text-ink-muted">
            Fonte: Portal da Transparência (CGU) — contratos do Poder Executivo Federal. Estados e municípios:
            <a href="https://pncp.gov.br/app/contratos?q={{ $cnpj }}&pagina=1" target="_blank" rel="noopener noreferrer" class="font-medium text-ink-2 underline hover:text-ink">pesquisar no PNCP</a>
            (a API do PNCP não permite buscar por fornecedor; o link abre a busca do próprio site).
        </p>
    </x-ui.card>
</div>
