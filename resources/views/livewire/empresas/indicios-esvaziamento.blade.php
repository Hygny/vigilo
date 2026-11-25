{{-- Indícios de esvaziamento e sucessão: o que mudou depois do marco da dívida. Espera $r (EsvaziamentoReport). --}}
@php
    $fmt = fn (?string $d): string => $d ? \Illuminate\Support\Carbon::parse($d)->format('d/m/Y') : '—';
    $brl = fn (float $v): string => 'R$ '.number_format($v, 2, ',', '.');
    $pgfn = $r->origemMarco === \App\DTO\Esvaziamento\EsvaziamentoReport::MARCO_PGFN;
    $informado = $r->origemMarco === \App\DTO\Esvaziamento\EsvaziamentoReport::MARCO_INFORMADO;
    $nivel = $r->semMarco() ? null : $r->nivel();
@endphp
<div>
    <x-ui.card class="p-[22px]">
        <div class="mb-3 flex flex-wrap items-center gap-2">
            <x-ui.icon name="move_down" :size="20" class="text-accent" />
            <h2 class="text-[15px] font-semibold text-ink">Indícios de esvaziamento e sucessão</h2>
            @if ($nivel === \App\DTO\Esvaziamento\EsvaziamentoReport::NIVEL_ALTO)
                <x-ui.badge tone="crit" dot>Indícios fortes</x-ui.badge>
            @elseif ($nivel === \App\DTO\Esvaziamento\EsvaziamentoReport::NIVEL_MEDIO)
                <x-ui.badge tone="med" dot>Indícios</x-ui.badge>
            @elseif ($nivel === \App\DTO\Esvaziamento\EsvaziamentoReport::NIVEL_PARCIAL)
                <x-ui.badge tone="muted">Consulta parcial</x-ui.badge>
            @elseif ($nivel === \App\DTO\Esvaziamento\EsvaziamentoReport::NIVEL_NENHUM)
                <x-ui.badge tone="muted">Nada encontrado</x-ui.badge>
            @endif
        </div>

        {{-- Marco --}}
        <div class="mb-4 rounded-btn border border-line bg-surface-2 px-4 py-3 text-[13px] text-ink-2">
            @if ($r->semMarco())
                <p>
                    {{ $r->pgfnConsultada ? 'Sem inscrição em dívida ativa da União para usar como marco.' : 'Dívida ativa da União indisponível agora.' }}
                    Informe a data da dívida (vencimento do título, inadimplência do contrato) para procurar o que mudou depois dela.
                </p>
            @elseif ($pgfn)
                <p><span class="font-medium">Marco:</span> 1ª inscrição em dívida ativa da União em <span class="font-medium">{{ $fmt($r->marco) }}</span>@if ($r->dividaTotal !== null) — hoje {{ $brl($r->dividaTotal) }} em {{ $r->inscricoes }} inscrição(ões)@endif.</p>
            @else
                <p><span class="font-medium">Marco:</span> data informada, <span class="font-medium">{{ $fmt($r->marco) }}</span>.</p>
            @endif
            <form wire:submit="aplicarData" class="mt-2 flex flex-wrap items-center gap-2">
                <label for="data-divida-{{ $cnpj }}" class="text-[12.5px] text-ink-muted">{{ $r->semMarco() ? 'Data da dívida' : 'Usar outra data' }}</label>
                <input id="data-divida-{{ $cnpj }}" type="date" wire:model="data" min="1990-01-01" max="{{ now()->toDateString() }}" class="rounded-btn border border-line bg-surface px-2 py-1 text-sm text-ink" />
                <x-ui.button type="submit" variant="secondary" size="sm">Aplicar</x-ui.button>
                @if ($informado)
                    <button type="button" wire:click="usarPgfn" class="text-[12.5px] font-medium text-ink-muted hover:text-ink-2">voltar à dívida ativa</button>
                @endif
                @error('data') <p class="w-full text-[12.5px] text-danger">{{ $message }}</p> @enderror
            </form>
        </div>

        @unless ($r->semMarco())
            @if ($nivel === \App\DTO\Esvaziamento\EsvaziamentoReport::NIVEL_PARCIAL)
                <p class="mb-4 text-[12.5px] text-ink-muted">Nenhum indício no que foi lido, mas a consulta não foi completa (parte não respondeu agora ou havia registros demais para ler de uma vez) — não dá para dizer que não há nada.</p>
            @endif
            @if ($r->indicios() !== [])
                <div class="mb-4 flex flex-wrap gap-2 text-[12.5px]">
                    @foreach ($r->indicios() as $i)
                        <span class="rounded-full bg-surface-2 px-3 py-1 text-ink-2 ring-1 ring-inset ring-line">{{ $i }}</span>
                    @endforeach
                </div>
            @endif

            {{-- Possíveis sucessoras --}}
            <h3 class="mb-2 text-[11px] font-semibold uppercase tracking-wide text-ink-muted">Possíveis sucessoras e empresas do mesmo grupo (depois da dívida)</h3>
            @if ($r->foraDaBase)
                <p class="text-sm text-ink-muted">Empresa ainda não está na base da Receita (aberta depois da última atualização) — sem como procurar sucessoras agora.</p>
            @elseif ($r->sucessoras === null)
                <p class="text-sm text-ink-muted">Varredura indisponível no momento — não consultado.</p>
            @elseif ($r->sucessoras === [])
                <p class="text-sm text-ink-muted">Nenhuma empresa surgiu depois da dívida com dois ou mais sinais em comum (endereço, ramo, sócio, contato).</p>
            @else
                <ul class="divide-y divide-line">
                    @foreach ($r->sucessoras as $s)
                        <li class="py-2.5 text-sm">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="flex min-w-0 items-center gap-2">
                                    <a href="{{ route('empresas.perfil', $s->cnpj) }}" wire:navigate class="truncate font-medium text-ink hover:underline">{{ $s->nome }}</a>
                                    @if ($s->forte())
                                        <x-ui.badge tone="crit">forte</x-ui.badge>
                                    @else
                                        <x-ui.badge tone="med">médio</x-ui.badge>
                                    @endif
                                </span>
                                <span class="text-[12.5px] {{ $s->situacao && $s->situacao !== 'ATIVA' ? 'text-danger' : 'text-ink-muted' }}">
                                    {{ $s->abertaAposMarco ? ($s->filialNoEndereco ? 'filial no endereço aberta em ' : 'aberta em ').$fmt($s->inicio) : 'sócio entrou em '.$fmt($s->entradaAposMarco) }} · {{ $s->situacao ?? '—' }}
                                </span>
                            </div>
                            <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                @foreach ($s->rotulos() as $rot)
                                    <span class="rounded-md bg-surface-2 px-1.5 py-0.5 text-[11px] text-ink-2 ring-1 ring-inset ring-line">{{ $rot }}</span>
                                @endforeach
                                @if ($s->sociosEmComum !== [])
                                    <span class="text-[11.5px] text-ink-muted">· {{ implode(', ', array_slice($s->sociosEmComum, 0, 3)) }}{{ count($s->sociosEmComum) > 3 ? ' e mais '.(count($s->sociosEmComum) - 3) : '' }}</span>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
                @if ($r->totalSucessoras > count($r->sucessoras))
                    <p class="mt-2 text-[12px] text-ink-muted">Mostrando {{ count($r->sucessoras) }} de {{ $r->totalSucessoras }} (mais sinais primeiro).</p>
                @endif
            @endif
            @if ($r->outrasLigadas > 0)
                <p class="mt-2 text-[12px] text-ink-muted">Mais {{ $r->outrasLigadas }} empresa(s) ligada(s) surgiram depois da dívida com um sinal só (ou só endereço e contato, típico de escritório de contabilidade) — não listadas.</p>
            @endif
            @if ($r->parcial)
                <p class="mt-1 text-[12px] text-ink-muted">Leitura parcial: o endereço, os sócios, o histórico ou as participações têm registros demais para ler de uma vez — pode haver mais.</p>
            @endif

            {{-- Encerramento --}}
            @if ($r->encerradaSituacao !== null)
                <h3 class="mb-2 mt-5 text-[11px] font-semibold uppercase tracking-wide text-ink-muted">Encerramento</h3>
                <p class="text-sm text-ink-2">{{ ucfirst(mb_strtolower($r->encerradaSituacao)) }} em {{ $fmt($r->encerradaEm) }}, depois da dívida.</p>
            @endif

            {{-- Quadro societário --}}
            <h3 class="mb-2 mt-5 text-[11px] font-semibold uppercase tracking-wide text-ink-muted">Quadro societário depois da dívida</h3>
            @if ($r->entradas === null)
                <p class="text-sm text-ink-muted">Quadro societário indisponível no momento.</p>
            @else
                @if ($r->quadroTodoAposMarco)
                    <p class="mb-1 text-sm font-medium text-danger">Todos os sócios atuais entraram depois da dívida.</p>
                @endif
                @if ($r->entradas === [])
                    <p class="text-sm text-ink-muted">Nenhum sócio atual entrou depois da dívida.</p>
                @else
                    <ul class="divide-y divide-line">
                        @foreach ($r->entradas as $m)
                            <li class="flex flex-wrap items-center justify-between gap-2 py-2 text-sm">
                                <span class="font-medium text-ink">{{ $m->nome }}</span>
                                <span class="text-[12.5px] text-ink-muted">{{ collect([$m->qualificacao, 'entrou em '.$fmt($m->data)])->filter()->implode(' · ') }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            @endif
            @if ($r->saidas === null)
                <p class="mt-2 text-sm text-ink-muted">Histórico de saídas de sócio indisponível no momento.</p>
            @elseif ($r->saidas !== [])
                <ul class="mt-2 divide-y divide-line">
                    @foreach ($r->saidas as $m)
                        <li class="flex flex-wrap items-center justify-between gap-2 py-2 text-sm">
                            <span class="font-medium text-ink">{{ $m->nome }}</span>
                            <span class="text-[12.5px] text-ink-muted">saída detectada em {{ $fmt($m->data) }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif

            {{-- Capital --}}
            @if (($r->reducoesCapital ?? []) !== [])
                <h3 class="mb-2 mt-5 text-[11px] font-semibold uppercase tracking-wide text-ink-muted">Capital social</h3>
                <ul class="divide-y divide-line">
                    @foreach ($r->reducoesCapital ?? [] as $c)
                        <li class="py-2 text-sm text-ink-2">Reduzido de {{ $brl($c->de) }} para {{ $brl($c->para) }} <span class="text-ink-muted">(detectado em {{ $fmt($c->data) }})</span></li>
                    @endforeach
                </ul>
            @endif

            {{-- Participações novas --}}
            <h3 class="mb-2 mt-5 text-[11px] font-semibold uppercase tracking-wide text-ink-muted">Participações adquiridas depois da dívida</h3>
            @if ($r->participacoes === null)
                <p class="text-sm text-ink-muted">Indisponível no momento.</p>
            @elseif ($r->participacoes === [])
                <p class="text-sm text-ink-muted">A empresa não entrou como sócia de outras depois da dívida.</p>
            @else
                <ul class="divide-y divide-line">
                    @foreach (array_slice($r->participacoes, 0, \App\DTO\Esvaziamento\EsvaziamentoReport::PARTICIPACOES_EXIBIDAS) as $p)
                        <li class="flex flex-wrap items-center justify-between gap-2 py-2 text-sm">
                            <a href="{{ route('empresas.perfil', $p->cnpj) }}" wire:navigate class="truncate font-medium text-ink hover:underline">{{ $p->nome }}</a>
                            <span class="text-[12.5px] text-ink-muted">entrou em {{ $fmt($p->dataEntrada) }} · {{ $p->situacao ?? '—' }}</span>
                        </li>
                    @endforeach
                </ul>
                @if (count($r->participacoes) > \App\DTO\Esvaziamento\EsvaziamentoReport::PARTICIPACOES_EXIBIDAS)
                    <p class="mt-2 text-[12px] text-ink-muted">Mostrando {{ \App\DTO\Esvaziamento\EsvaziamentoReport::PARTICIPACOES_EXIBIDAS }} de {{ count($r->participacoes) }} — todas as participações estão no <a href="{{ route('empresas.perfil', $cnpj) }}" wire:navigate class="font-medium underline">perfil público</a>.</p>
                @endif
            @endif

            <p class="mt-4 text-[12px] text-ink-muted">
                Indícios em dados abertos (Receita e PGFN), não prova: orientam um pedido de redirecionamento, sucessão ou desconsideração da personalidade jurídica, que precisa de documentos.
                @if ($r->historicoDesde)
                    Saídas de sócio e mudanças de capital só aparecem desde {{ \Illuminate\Support\Carbon::createFromFormat('!Y-m', $r->historicoDesde)->format('m/Y') }}, quando o histórico começou.
                @endif
            </p>
        @endunless
    </x-ui.card>
</div>
