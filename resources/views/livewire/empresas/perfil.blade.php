<div class="py-8">
    <div class="mx-auto max-w-[1000px] space-y-5 px-5 sm:px-8">
        <a href="{{ route('empresas.busca') }}" wire:navigate class="inline-flex items-center gap-1 text-[13px] font-medium text-ink-muted hover:text-ink-2">
            <x-ui.icon name="arrow_back" :size="17" />Voltar à busca
        </a>

        @if ($limitado)
            <div class="flex items-start gap-2 rounded-btn border border-line bg-surface px-4 py-3 text-sm text-ink-2 shadow-card">
                <x-ui.icon name="hourglass_top" :size="18" class="mt-0.5 text-ink-muted" />Muitos perfis abertos em pouco tempo. Aguarde um minuto e recarregue a página.
            </div>
        @elseif ($indisponivel)
            <div class="flex items-start gap-2 rounded-btn border border-line bg-surface px-4 py-3 text-sm text-ink-2 shadow-card">
                <x-ui.icon name="cloud_off" :size="18" class="mt-0.5 text-ink-muted" />Base CNPJ indisponível no momento. Tente novamente em instantes.
            </div>
        @elseif ($empresa === null)
            <x-ui.card class="p-[22px]">
                <h1 class="text-[20px] font-bold text-ink">CNPJ não encontrado</h1>
                <p class="mt-1 text-sm text-ink-muted">O CNPJ <span class="font-mono">{{ $formatado }}</span> não está na base da Receita Federal.</p>
            </x-ui.card>
        @else
            @php($razao = \App\Support\RazaoSocial::exibir($empresa->razaoSocial))
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-[26px] font-bold tracking-[-0.03em] text-ink">{{ $razao ?? $empresa->nomeFantasia ?? 'Empresa' }}</h1>
                        @if ($empresa->situacao !== '')
                            <x-ui.badge :tone="$empresa->situacao === 'ATIVA' ? 'ok' : 'crit'">{{ $empresa->situacao }}</x-ui.badge>
                        @endif
                    </div>
                    @if ($empresa->nomeFantasia && $empresa->nomeFantasia !== $razao)
                        <p class="mt-0.5 text-sm text-ink-2">{{ $empresa->nomeFantasia }}</p>
                    @endif
                    <p class="mt-1 font-mono text-sm text-ink-muted">{{ $formatado }}</p>
                </div>
                @if ($monitorada)
                    <div class="flex flex-wrap items-center gap-2">
                        <x-ui.button :href="route('companies.show', $monitorada)" icon="visibility" wire:navigate>Ver na carteira</x-ui.button>
                        <x-ui.button :href="route('companies.graph', $monitorada)" variant="secondary" icon="hub" wire:navigate>Grafo societário</x-ui.button>
                    </div>
                @endif
            </div>

            @if (session('status'))
                <div class="flex items-center gap-2 rounded-btn border border-line bg-surface px-4 py-3 text-sm text-ink-2 shadow-card">
                    <x-ui.icon name="check_circle" :size="18" class="text-ok" />{{ session('status') }}
                </div>
            @endif

            @unless ($monitorada)
                <form wire:submit="adicionar" class="flex flex-wrap items-end gap-3 rounded-card border border-line bg-surface p-[18px] shadow-card">
                    <div class="flex items-center gap-2 text-[13.5px] text-ink-2">
                        <x-ui.icon name="add_alert" :size="19" class="text-accent" />Monitorar esta empresa e receber alertas de mudança
                    </div>
                    @if ($portfolios->isEmpty())
                        <p class="text-[13px] text-ink-muted">Crie um portfólio primeiro, em <a href="{{ route('portfolios.index') }}" wire:navigate class="font-medium text-ink underline">Portfólios</a>.</p>
                    @else
                        <select wire:model="portfolioId" class="rounded-btn border border-line bg-surface px-3 py-2 text-sm text-ink-2" aria-label="Portfólio">
                            <option value="">Escolha o portfólio…</option>
                            @foreach ($portfolios as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                        <x-ui.button type="submit" icon="add">Adicionar à carteira</x-ui.button>
                    @endif
                    @error('portfolioId') <p class="w-full text-sm text-danger">{{ $message }}</p> @enderror
                </form>
            @endunless

            {{-- Dados cadastrais --}}
            <x-ui.card class="p-[22px]">
                <div class="mb-4 flex items-center gap-2"><x-ui.icon name="description" :size="20" class="text-accent" /><h2 class="text-[15px] font-semibold text-ink">Dados cadastrais (Receita Federal)</h2></div>
                @php($end = $empresa->endereco)
                <dl class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                    <div><dt class="text-[11px] font-semibold uppercase tracking-wide text-ink-muted">Situação cadastral</dt><dd class="mt-0.5 text-sm text-ink-2">{{ $empresa->situacao !== '' ? $empresa->situacao : '—' }} @if ($empresa->dataSituacao)<span class="text-ink-muted">({{ \Illuminate\Support\Carbon::parse($empresa->dataSituacao)->format('d/m/Y') }})</span>@endif</dd></div>
                    <div><dt class="text-[11px] font-semibold uppercase tracking-wide text-ink-muted">Abertura</dt><dd class="mt-0.5 text-sm text-ink-2">{{ $empresa->dataAbertura ? \Illuminate\Support\Carbon::parse($empresa->dataAbertura)->format('d/m/Y') : '—' }}</dd></div>
                    <div><dt class="text-[11px] font-semibold uppercase tracking-wide text-ink-muted">CNAE principal</dt><dd class="mt-0.5 text-sm text-ink-2">{{ collect([$empresa->cnaeCodigo, $empresa->cnaeDescricao])->filter()->implode(' — ') ?: '—' }}</dd></div>
                    <div><dt class="text-[11px] font-semibold uppercase tracking-wide text-ink-muted">Endereço</dt><dd class="mt-0.5 text-sm text-ink-2">{{ collect([trim($end->tipoLogradouro.' '.$end->logradouro), $end->numero, $end->complemento, $end->bairro, collect([$end->municipio, $end->uf])->filter()->implode('/'), $end->cep])->filter()->implode(' · ') ?: '—' }}</dd></div>
                </dl>
            </x-ui.card>

            {{-- Dívida ativa (PGFN) --}}
            <x-ui.card class="p-[22px]">
                <div class="mb-3 flex items-center gap-2"><x-ui.icon name="account_balance" :size="20" class="text-accent" /><h2 class="text-[15px] font-semibold text-ink">Dívida ativa da União (PGFN)</h2></div>
                @if (! $empresa->dividaConsultada)
                    <p class="text-sm text-ink-muted">Não foi possível consultar a dívida ativa agora.</p>
                @elseif ($empresa->dividaAtiva === null)
                    <div class="flex items-start gap-2 rounded-btn bg-ok-soft px-4 py-3 text-sm text-ok">
                        <x-ui.icon name="verified_user" :size="18" class="mt-0.5" /><p>Nenhuma inscrição em dívida ativa para esta empresa (matriz e filiais).</p>
                    </div>
                @else
                    <div class="flex items-start gap-2 rounded-btn bg-med-soft px-4 py-3 text-sm text-med">
                        <x-ui.icon name="account_balance" :size="18" class="mt-0.5" />
                        <p><span class="font-semibold">{{ $empresa->dividaAtiva->resumo() }}</span>@if ($empresa->dividaAtiva->ajuizadas > 0) · {{ $empresa->dividaAtiva->ajuizadas }} ajuizada(s)@endif</p>
                    </div>
                @endif
            </x-ui.card>

            {{-- Quadro societário --}}
            <x-ui.card class="p-[22px]">
                <div class="mb-3 flex items-center gap-2"><x-ui.icon name="groups" :size="20" class="text-accent" /><h2 class="text-[15px] font-semibold text-ink">Quadro societário</h2></div>
                @if ($empresa->socios === [])
                    <p class="text-sm text-ink-muted">Sem sócios registrados na Receita.</p>
                @else
                    <ul class="divide-y divide-line">
                        @foreach ($empresa->socios as $socio)
                            <li class="flex flex-wrap items-center justify-between gap-2 py-2.5 text-sm">
                                <span class="font-medium text-ink">{{ $socio->nome ?? 'Sócio' }} @if ($socio->documentoMascarado)<span class="font-mono text-[12.5px] text-ink-muted">({{ $socio->documentoMascarado }})</span>@endif</span>
                                <span class="text-ink-muted">{{ collect([$socio->qualificacao, $socio->dataEntrada ? 'desde '.\Illuminate\Support\Carbon::parse($socio->dataEntrada)->format('d/m/Y') : null])->filter()->implode(' · ') }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ui.card>

            {{-- Histórico cadastral --}}
            <x-ui.card class="p-[22px]">
                <div class="mb-3 flex items-center gap-2"><x-ui.icon name="history" :size="20" class="text-accent" /><h2 class="text-[15px] font-semibold text-ink">Histórico cadastral</h2></div>
                @if ($historico === null)
                    <p class="text-sm text-ink-muted">Histórico indisponível no momento.</p>
                @else
                    @if ($historico->alertaEndereco())
                        <div class="mb-3 flex items-start gap-2 rounded-btn bg-high-soft px-4 py-3 text-sm text-high">
                            <x-ui.icon name="warning" :size="18" class="mt-0.5" /><p>{{ $historico->mudancasEndereco12m() }} mudanças de endereço nos últimos 12 meses.</p>
                        </div>
                    @endif
                    @if ($historico->eventos === [])
                        <p class="text-sm text-ink-muted">Nenhuma mudança registrada{{ $historico->desde ? ' desde '.$historico->desde : '' }}.</p>
                    @else
                        <ol class="space-y-3">
                            @foreach ($historico->eventos as $evento)
                                <li class="text-sm">
                                    <p class="text-[12px] text-ink-muted">{{ \Illuminate\Support\Carbon::parse($evento->detectadoEm)->format('d/m/Y') }}@if ($evento->estabelecimento && $evento->estabelecimento !== $cnpj) · estabelecimento {{ \App\Support\Cnpj::tryFrom($evento->estabelecimento)?->formatted() }}@endif</p>
                                    <p class="text-ink-2"><span class="font-semibold text-ink">{{ $evento->rotulo }}:</span>
                                        @if ($evento->antes !== null && $evento->depois !== null)
                                            <span class="text-ink-muted line-through">{{ $evento->antes }}</span> → {{ $evento->depois }}
                                        @else
                                            {{ $evento->depois ?? $evento->antes }}
                                        @endif
                                    </p>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                    <p class="mt-3 text-[12px] text-ink-muted">Mudanças detectadas entre os dumps mensais da Receita{{ $historico->desde ? ', registradas desde '.$historico->desde : '' }}.</p>
                @endif
            </x-ui.card>
        @endif
    </div>
</div>
