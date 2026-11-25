<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 32px 36px; }
        * { font-family: "DejaVu Sans", sans-serif; }
        body { color: #1A1A1A; font-size: 11px; line-height: 1.45; }
        h1 { font-size: 20px; margin: 0; letter-spacing: -0.5px; }
        h2 { font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #7A7A7A; border-bottom: 1px solid #E5E5E5; padding-bottom: 3px; margin: 18px 0 8px; }
        .muted { color: #7A7A7A; }
        .header { border-bottom: 2px solid #1A1A1A; padding-bottom: 10px; margin-bottom: 6px; }
        .brand { font-size: 12px; font-weight: bold; color: #B45309; }
        table.kv { width: 100%; border-collapse: collapse; }
        table.kv td { padding: 3px 0; vertical-align: top; }
        table.kv td.k { width: 34%; color: #7A7A7A; }
        .box { border-radius: 6px; padding: 10px 12px; margin: 6px 0; }
        .pill { display: inline-block; border-radius: 10px; padding: 2px 10px; font-weight: bold; font-size: 11px; }
        ul { margin: 4px 0; padding-left: 16px; }
        li { margin: 2px 0; }
        .footer { margin-top: 22px; border-top: 1px solid #E5E5E5; padding-top: 6px; font-size: 9px; color: #9A9A9A; }
    </style>
</head>
<body>
    <div class="header">
        <table style="width:100%"><tr>
            <td><h1>Dossiê de Due Diligence</h1></td>
            <td style="text-align:right"><span class="brand">VIGILO</span></td>
        </tr></table>
    </div>
    <p class="muted" style="margin:0 0 2px">Gerado em {{ $geradoEm->format('d/m/Y H:i') }}</p>

    <h2>Empresa</h2>
    <table class="kv">
        <tr><td class="k">Razão social</td><td>{{ \App\Support\RazaoSocial::exibir($snapshot?->razao_social) ?? $company->label ?? '—' }}</td></tr>
        <tr><td class="k">CNPJ</td><td>{{ $company->formattedCnpj() }}</td></tr>
        @if ($company->label)<tr><td class="k">Rótulo interno</td><td>{{ $company->label }}</td></tr>@endif
    </table>

    @php
        $cores = [
            'baixo' => ['#166534', '#DCFCE7'],
            'medio' => ['#92400E', '#FEF3C7'],
            'alto' => ['#991B1B', '#FEE2E2'],
        ];
        $cor = $assessment->nivel ? ($cores[$assessment->nivel->value] ?? ['#1A1A1A', '#F2F2F2']) : null;
    @endphp

    <h2>Nível de risco</h2>
    @if ($assessment->avaliado() && $cor)
        <div class="box" style="background: {{ $cor[1] }};">
            <span class="pill" style="background: {{ $cor[0] }}; color: #FFFFFF;">Risco {{ $assessment->nivel->label() }}</span>
            <span style="color: {{ $cor[0] }}; margin-left: 8px;">{{ $assessment->motivo }}</span>
        </div>
    @else
        <p class="muted">Não avaliado — a consulta de sanções não está disponível para esta empresa no momento.</p>
    @endif

    <h2>Dados cadastrais</h2>
    @if ($snapshot === null)
        <p class="muted">Nenhuma coleta registrada ainda.</p>
    @else
        <table class="kv">
            <tr><td class="k">Situação cadastral</td><td>{{ $snapshot->situacao_cadastral }}@if ($snapshot->situacao_data) <span class="muted">({{ $snapshot->situacao_data->format('d/m/Y') }})</span>@endif</td></tr>
            <tr><td class="k">Nome fantasia</td><td>{{ $snapshot->nome_fantasia ?? '—' }}</td></tr>
            <tr><td class="k">CNAE principal</td><td>{{ $snapshot->cnae_principal ?? '—' }}</td></tr>
            <tr><td class="k">Porte</td><td>{{ $snapshot->porte ?? '—' }}</td></tr>
            <tr><td class="k">Natureza jurídica</td><td>{{ $snapshot->natureza_juridica ?? '—' }}</td></tr>
            <tr><td class="k">Endereço</td><td>{{ collect([$snapshot->logradouro, $snapshot->municipio, $snapshot->uf])->filter()->implode(' · ') ?: '—' }}</td></tr>
            @if ($detalhes !== null)
                @if ($detalhes->capitalTexto())<tr><td class="k">Capital social</td><td>{{ $detalhes->capitalTexto() }}</td></tr>@endif
                @if ($detalhes->simples)<tr><td class="k">Simples Nacional</td><td>{{ $detalhes->simples->texto() }}</td></tr>@endif
                @if ($detalhes->mei)<tr><td class="k">MEI</td><td>{{ $detalhes->mei->texto() }}</td></tr>@endif
            @endif
        </table>

        <h2>Quadro societário</h2>
        @if ($snapshot->partners->isEmpty())
            <p class="muted">Sem sócios registrados na última coleta.</p>
        @else
            <ul>
                @foreach ($snapshot->partners as $partner)
                    <li>{{ $partner->nome }}@if ($partner->qualificacao) <span class="muted">— {{ $partner->qualificacao }}</span>@endif</li>
                @endforeach
            </ul>
        @endif
    @endif

    @if ($beneficiarios !== [])
        <h2>Beneficiários finais (estrutura de controle)</h2>
        <ul>
            @foreach ($beneficiarios as $b)
                @php($meta = collect(['nível '.$b->depth, $b->document, $b->type === 'ext' ? 'estrangeiro' : null])->filter()->implode(' · '))
                <li>{{ $b->name }} <span class="muted">— {{ $meta }}</span></li>
            @endforeach
        </ul>
        <p class="muted" style="font-size:9px;">Estrutural — sem % de participação (não consta na base aberta da Receita).</p>
    @endif

    @foreach (['Participações (empresas de que é sócia)' => $participacoes, 'Grupo econômico (empresas ligadas por sócio)' => $grupo] as $tituloLigadas => $ligadas)
        @if ($ligadas !== [])
            <h2>{{ $tituloLigadas }}</h2>
            <ul>
                @foreach ($ligadas as $g)
                    @php($meta = collect([$g['document'], $g['situacao']])->filter()->implode(' · '))
                    <li>{{ $g['label'] }} <span class="muted">{{ $meta }}</span>@if (! $g['pgfn_consultada']) <span class="muted">— dívida ativa não consultada</span>@elseif ($g['divida']) <span style="color:#92400E;">— dívida ativa: {{ $g['divida']->resumo() }}</span>@endif</li>
                @endforeach
            </ul>
            @if ($ligadas === $participacoes && $participacoesTotal > count($participacoes))
                <p class="muted" style="font-size:9px;">Listando {{ count($participacoes) }} de {{ $participacoesTotal }} participações (ativas primeiro).</p>
            @endif
        @endif
    @endforeach

    <h2>Risco ao redor (empresas ligadas)</h2>
    @if ($aoRedor === null)
        <p class="muted">Indisponível no momento.</p>
    @elseif ($aoRedor->total() === 0)
        <p class="muted">Sem empresas ligadas por sócio PJ ou participação.</p>
    @else
        @if ($aoRedor->selo())
            <p style="color:#92400E;">{{ $aoRedor->selo() }}.</p>
            <ul>
                @foreach (array_slice($aoRedor->comAlerta(), 0, 15) as $e)
                    <li>{{ $e->nome }} <span class="muted">{{ $e->cnpj }} · {{ $e->ligacao }}</span> — {{ implode(', ', array_map(fn ($c) => $e->rotulo($c), $e->codigos())) }}</li>
                @endforeach
            </ul>
        @elseif ($aoRedor->semSinalConfiavel())
            <p>Nenhuma das {{ $aoRedor->total() }} empresa(s) ligada(s) tem Lista Suja, sanção, dívida ativa ou situação negativa.</p>
        @else
            {{-- Fonte fora ou cópia da CGU velha: sem "nenhuma" (mesma regra da tela). --}}
            <p>Nenhum sinal entre as {{ $aoRedor->total() }} empresa(s) ligada(s) nas fontes que puderam ser consultadas.</p>
        @endif
        @if ($aoRedor->naoConsultadas() !== [])
            <p class="muted" style="font-size:9px;">Não consultado agora: {{ implode(', ', $aoRedor->naoConsultadas()) }}.</p>
        @endif
        @if ($aoRedor->sancoesDesatualizadas)
            <p style="color:#92400E; font-size:9px;">A cópia das sanções da CGU está desatualizada — sanção recente pode não aparecer.</p>
        @endif
    @endif

    @if ($filiais !== [])
        <h2>Filiais</h2>
        <ul>
            @foreach ($filiais as $f)
                @php($meta = collect([$f->isMatriz ? 'MATRIZ' : 'FILIAL', $f->situacao, $f->uf, $f->municipio])->filter()->implode(' · '))
                <li>{{ $f->cnpj }} <span class="muted">{{ $meta }}</span></li>
            @endforeach
        </ul>
    @endif

    @if ($historico !== null)
        <h2>Histórico cadastral (Receita)</h2>
        @if ($historico->eventos === [])
            <p class="muted">Nenhuma mudança cadastral registrada até agora.</p>
        @else
            <ul>
                @foreach ($historico->eventos as $evento)
                    <li>
                        <span class="muted">{{ \Illuminate\Support\Carbon::parse($evento->detectadoEm)->format('d/m/Y') }}</span>
                        <strong>{{ $evento->rotulo }}</strong>:
                        @if ($evento->antes !== null && $evento->depois !== null)
                            {{ $evento->antes }} → {{ $evento->depois }}
                        @else
                            {{ $evento->depois ?? $evento->antes }}
                        @endif
                    </li>
                @endforeach
            </ul>
            @if ($historico->alertaEndereco())
                <p style="color:#92400E; font-size:10px;">Atenção: {{ $historico->mudancasEndereco12m() }} mudanças de endereço nos últimos 12 meses.</p>
            @endif
        @endif
        <p class="muted" style="font-size:9px;">Mudanças detectadas entre os dumps mensais da Receita Federal{{ $historico->desde ? ', registradas desde '.$historico->desdeMesAno() : '' }}.</p>
    @endif

    @if ($assessment->listaSuja)
        <h2>Trabalho escravo (Lista Suja — MTE)</h2>
        <div class="box" style="background: #FEE2E2;">
            <strong style="color:#991B1B;">Consta na Lista Suja do trabalho escravo.</strong>
            @php($detalhe = collect([$assessment->listaSuja->nome, $assessment->listaSuja->uf, $assessment->listaSuja->ano ? 'ano '.$assessment->listaSuja->ano : null])->filter()->implode(' · '))
            @if ($detalhe)<div style="color:#991B1B; font-size:10px; margin-top:2px;">{{ $detalhe }}</div>@endif
        </div>
    @endif

    @if (! $assessment->pgfnDisponivel)
        <h2>Dívida ativa (PGFN)</h2>
        <p class="muted">Não foi possível consultar a dívida ativa no momento da geração — confira de novo antes de concluir.</p>
    @elseif ($assessment->pgfn)
        <h2>Dívida ativa (PGFN)</h2>
        <div class="box" style="background: #FEF3C7;">
            <strong style="color:#92400E;">Consta dívida ativa na União.</strong>
            <div style="color:#92400E; font-size:10px; margin-top:2px;">{{ $assessment->pgfn->resumo() }}@if ($assessment->pgfn->ajuizadas > 0) · {{ $assessment->pgfn->ajuizadas }} ajuizada(s)@endif</div>
        </div>
    @endif

    <h2>Sanções (CGU — CEIS/CNEP/CEPIM)</h2>
    @if (! $assessment->report->configurado || ! $assessment->report->disponivel)
        <p class="muted">Consulta de sanções indisponível no momento.</p>
    @elseif (! $assessment->report->temSancoes())
        <p>Nenhuma sanção encontrada nos cadastros CEIS, CNEP e CEPIM para este CNPJ.</p>
    @else
        <ul>
            @foreach ($assessment->report->sancoes as $sancao)
                <li>
                    <strong>{{ $sancao->fonte->sigla() }}</strong> — {{ $sancao->tipo ?? 'Sanção registrada' }}
                    @if ($sancao->dataInicio) <span class="muted">(desde {{ \Illuminate\Support\Carbon::parse($sancao->dataInicio)->format('d/m/Y') }}@if ($sancao->dataFim) até {{ \Illuminate\Support\Carbon::parse($sancao->dataFim)->format('d/m/Y') }}@endif)</span>@endif
                    @if ($sancao->orgao)<div class="muted" style="font-size:10px;">Aplicada por: {{ $sancao->orgao }}</div>@endif
                </li>
            @endforeach
        </ul>
    @endif

    @php($contratos = $bens?->contratos)
    <h2>Contratos públicos (fornecedora do governo federal)</h2>
    @if ($contratos === null || ! $contratos->configurado || ! $contratos->disponivel)
        <p class="muted">Contratos públicos não consultados agora.</p>
    @elseif ($contratos->total() === 0)
        <p>Nenhum contrato do Executivo Federal como fornecedora.</p>
    @else
        <p>{{ $contratos->truncado ? 'Ao menos ' : '' }}{{ $contratos->total() }} contrato(s), {{ 'R$ '.number_format($contratos->valorTotal(), 2, ',', '.') }} no total · {{ $contratos->vigentes() }} vigente(s).</p>
        <ul>
            @foreach ($contratos->destaques(10) as $c)
                <li>{{ $c->objeto ? \Illuminate\Support\Str::limit($c->objeto, 110) : 'Contrato' }} <span class="muted">— {{ $c->vigente() ? 'vigente' : 'encerrado' }} · {{ $c->detalhe() }}@if ($c->valor() !== null) · {{ 'R$ '.number_format($c->valor(), 2, ',', '.') }}@endif</span></li>
            @endforeach
        </ul>
    @endif

    <h2>Bens e recebíveis (onde procurar penhora)</h2>
    @if ($bens === null)
        <p class="muted">Indisponível no momento.</p>
    @else
        <table class="kv">
            <tr><td class="k">Aeronaves (RAB/ANAC)</td><td>
                @if ($bens->aeronaves === null) <span class="muted">não consultado</span>
                @else {{ $bens->totalAeronavesAtivas }} ativa(s){{ $bens->totalAeronavesAtivas > 0 ? ' · '.($bens->aeronavesTruncadas() ? 'ao menos ' : '').count($bens->aeronavesLivres()).' sem ônus' : '' }}
                @endif
            </td></tr>
            <tr><td class="k">Marcas (INPI)</td><td>
                @if ($bens->marcas === null) <span class="muted">não consultado</span>
                @else {{ $bens->marcasEmVigor() }} registro(s) em vigor · {{ $bens->totalMarcasAtivas() - $bens->marcasEmVigor() }} pedido(s) em andamento
                @endif
            </td></tr>
            <tr><td class="k">Participações (quotas/ações)</td><td>{{ $bens->participacoes === null ? 'não consultado' : $bens->totalParticipacoes.' empresa(s)' }}</td></tr>
            <tr><td class="k">Créditos a receber (contratos vigentes)</td><td>
                @if (! $bens->contratos->configurado || ! $bens->contratos->disponivel) <span class="muted">não consultado</span>
                @else {{ count($bens->contratosVigentes()) }} contrato(s) · {{ 'R$ '.number_format($bens->valorContratosVigentes(), 2, ',', '.') }}
                @endif
            </td></tr>
        </table>
        @if (($bens->aeronaves ?? []) !== [])
            <ul>
                @foreach (array_slice(array_values(array_filter($bens->aeronaves, fn ($a) => $a->ativa())), 0, 20) as $a)
                    <li>{{ $a->marca }} — {{ $a->descricao() }} <span class="muted">({{ $a->rotuloPapeis(empresa: true) }}@if ($a->percentual !== null) {{ number_format($a->percentual, 0, ',', '.') }}%@endif)</span>@if ($a->onus()) <span style="color:#92400E;">— {{ $a->onus() }}</span>@elseif ($a->livre()) — sem ônus @endif</li>
                @endforeach
            </ul>
        @endif
        @if ($bens->marcasExibidas() !== [])
            <ul>
                @foreach ($bens->marcasExibidas() as $m)
                    <li>{{ $m->nome ?? 'Marca sem elemento nominativo' }} <span class="muted">nº {{ $m->numero }} — {{ $m->emVigor() ? 'registro em vigor'.($m->dataVigencia ? ' até '.\Illuminate\Support\Carbon::parse($m->dataVigencia)->format('d/m/Y') : '') : 'pedido: '.$m->situacao }}</span></li>
                @endforeach
            </ul>
        @endif
        <p class="muted" style="font-size:9px;">Imóveis, veículos e contas bancárias não têm fonte aberta (cartórios/ONR, Detran, RENAJUD e SISBAJUD são pagos ou só por ordem judicial). Confirme a titularidade nas certidões antes de pedir penhora.</p>
    @endif

    <h2>Indícios de esvaziamento e sucessão</h2>
    @if ($indicios === null)
        <p class="muted">Indisponível no momento.</p>
    @elseif ($indicios->semMarco())
        <p class="muted">{{ $indicios->pgfnConsultada ? 'Sem inscrição em dívida ativa da União para usar como marco' : 'Dívida ativa da União indisponível agora' }} — informe a data da dívida no monitoramento de devedora para procurar o que mudou depois dela.</p>
    @else
        <p>Marco: {{ $indicios->origemMarco === \App\DTO\Esvaziamento\EsvaziamentoReport::MARCO_PGFN ? '1ª inscrição em dívida ativa da União' : 'data da dívida informada' }} em {{ \Illuminate\Support\Carbon::parse($indicios->marco)->format('d/m/Y') }}.
            @if ($indicios->nivel() === \App\DTO\Esvaziamento\EsvaziamentoReport::NIVEL_ALTO) <strong style="color:#991B1B;">Indícios fortes.</strong>
            @elseif ($indicios->nivel() === \App\DTO\Esvaziamento\EsvaziamentoReport::NIVEL_MEDIO) <strong style="color:#92400E;">Indícios.</strong>
            @elseif ($indicios->nivel() === \App\DTO\Esvaziamento\EsvaziamentoReport::NIVEL_PARCIAL) <span class="muted">Consulta parcial — não dá para dizer que não há nada.</span>
            @else <span class="muted">Nada encontrado.</span>
            @endif
        </p>
        @if ($indicios->indicios() !== [])
            <p>{{ implode(' · ', $indicios->indicios()) }}</p>
        @endif
        @if (($indicios->sucessoras ?? []) !== [])
            <ul>
                @foreach (array_slice($indicios->sucessoras, 0, 10) as $s)
                    <li>{{ $s->nome }} <span class="muted">{{ $s->cnpj }} — {{ $s->forte() ? 'forte' : 'médio' }}: {{ implode(', ', $s->rotulos()) }}{{ $s->abertaAposMarco ? ' · aberta em '.\Illuminate\Support\Carbon::parse((string) $s->inicio)->format('d/m/Y') : '' }}</span></li>
                @endforeach
            </ul>
        @endif
        @if ($indicios->encerradaSituacao)<p>{{ ucfirst(mb_strtolower($indicios->encerradaSituacao)) }} em {{ \Illuminate\Support\Carbon::parse((string) $indicios->encerradaEm)->format('d/m/Y') }}, depois da dívida.</p>@endif
        @if (($indicios->entradas ?? []) !== [])
            <p>Sócios que entraram depois da dívida{{ $indicios->quadroTodoAposMarco ? ' (todos os atuais)' : '' }}: {{ implode(', ', array_map(fn ($m) => $m->nome, $indicios->entradas)) }}.</p>
        @endif
        @if (($indicios->saidas ?? []) !== [])
            <p>Saídas de sócio depois da dívida: {{ implode(', ', array_map(fn ($m) => $m->nome, $indicios->saidas)) }}.</p>
        @endif
        <p class="muted" style="font-size:9px;">Indícios em dados abertos, não prova: orientam um pedido de redirecionamento, sucessão ou desconsideração da personalidade jurídica, que precisa de documentos.</p>
    @endif

    <div class="footer">
        Documento gerado automaticamente pelo Vigilo a partir de fontes públicas (Receita Federal, PGFN, Portal da Transparência/CGU, Lista Suja/MTE, ANAC e INPI). Dados sujeitos a atualização — confirme nas fontes oficiais antes de qualquer decisão.
    </div>
</body>
</html>
