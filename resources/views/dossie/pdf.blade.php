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
        <tr><td class="k">Razão social</td><td>{{ $snapshot?->razao_social ?? $company->label ?? '—' }}</td></tr>
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

    @if ($assessment->listaSuja)
        <h2>Trabalho escravo (Lista Suja — MTE)</h2>
        <div class="box" style="background: #FEE2E2;">
            <strong style="color:#991B1B;">Consta na Lista Suja do trabalho escravo.</strong>
            @php($detalhe = collect([$assessment->listaSuja->nome, $assessment->listaSuja->uf, $assessment->listaSuja->ano ? 'ano '.$assessment->listaSuja->ano : null])->filter()->implode(' · '))
            @if ($detalhe)<div style="color:#991B1B; font-size:10px; margin-top:2px;">{{ $detalhe }}</div>@endif
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

    <div class="footer">
        Documento gerado automaticamente pelo Vigilo a partir de fontes públicas (Receita Federal, Portal da Transparência/CGU e Lista Suja/MTE). Dados sujeitos a atualização — confirme nas fontes oficiais antes de qualquer decisão.
    </div>
</body>
</html>
