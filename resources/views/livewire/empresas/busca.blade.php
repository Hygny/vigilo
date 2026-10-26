<div class="py-8">
    <div class="mx-auto max-w-[1000px] space-y-5 px-5 sm:px-8">
        <div>
            <h1 class="text-[26px] font-bold tracking-[-0.03em] text-ink">Busca de empresas</h1>
            <p class="mt-1 text-sm text-ink-muted">Qualquer empresa do Brasil pelo nome (razão social ou nome fantasia) ou pelo CNPJ, direto da base da Receita Federal.</p>
        </div>

        <form wire:submit="buscar" class="rounded-card border border-line bg-surface p-[22px] shadow-card">
            <div class="flex flex-col gap-3 sm:flex-row">
                <div class="ui-input-wrap flex-1">
                    <x-ui.icon name="search" :size="19" class="text-ink-muted" />
                    <input wire:model="termo" type="search" autofocus placeholder="Ex.: padaria são josé, magazine luiza ou 00.000.000/0001-91" class="ui-input" aria-label="Nome ou CNPJ da empresa" />
                </div>
                <x-ui.button type="submit" icon="search">Buscar</x-ui.button>
            </div>
            <div class="mt-3 flex flex-wrap items-center gap-3 text-[13px]">
                <label class="flex items-center gap-2 text-ink-muted">UF
                    <select wire:model.live="uf" class="rounded-btn border border-line bg-surface py-1 pl-2.5 pr-9 text-ink-2">
                        <option value="">Todas</option>
                        @foreach ($ufs as $sigla)
                            <option value="{{ $sigla }}">{{ $sigla }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="flex items-center gap-2 text-ink-muted">Situação
                    <select wire:model.live="situacao" class="rounded-btn border border-line bg-surface py-1 pl-2.5 pr-9 text-ink-2">
                        <option value="">Todas</option>
                        @foreach ($situacoes as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
        </form>

        @if ($aviso)
            <div class="flex items-start gap-2 rounded-btn border border-line bg-surface px-4 py-3 text-sm text-ink-2 shadow-card">
                <x-ui.icon name="info" :size="18" class="mt-0.5 text-ink-muted" />{{ $aviso }}
            </div>
        @endif

        @if ($resultados !== null)
            <x-ui.card class="p-[22px]">
                <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <x-ui.icon name="domain" :size="20" class="text-accent" />
                        <h2 class="text-[15px] font-semibold text-ink">Resultados</h2>
                        <span class="text-[12.5px] text-ink-muted">· {{ count($resultados->itens) }}</span>
                    </div>
                    @if ($resultados->truncado)
                        <span class="text-[12.5px] text-ink-muted">Muitas empresas com esses termos — mostrando as mais parecidas. Refine com mais uma palavra ou a UF.</span>
                    @endif
                </div>

                @if ($resultados->itens === [])
                    <p class="text-sm text-ink-muted">Nenhuma empresa encontrada com esses termos.</p>
                @else
                    <ul class="divide-y divide-line">
                        @foreach ($resultados->itens as $r)
                            <li>
                                <a href="{{ route('empresas.perfil', $r->cnpj) }}" wire:navigate class="-mx-2 flex items-start justify-between gap-3 rounded-btn px-2 py-3 hover:bg-surface-2">
                                    <div class="min-w-0 flex-1">
                                        <p class="font-medium text-ink">{{ $r->razaoSocial ?? $r->nomeFantasia ?? 'Empresa sem nome' }}</p>
                                        @if ($r->nomeFantasia && $r->nomeFantasia !== $r->razaoSocial)
                                            <p class="text-[13px] text-ink-2">{{ $r->nomeFantasia }}</p>
                                        @endif
                                        <p class="mt-0.5 font-mono text-[12.5px] text-ink-muted">
                                            {{ \App\Support\Cnpj::tryFrom($r->cnpj)?->formatted() ?? $r->cnpj }}
                                            · {{ $r->matriz ? 'Matriz' : 'Filial' }}
                                            @if ($r->municipio || $r->uf) · {{ collect([$r->municipio, $r->uf])->filter()->implode('/') }} @endif
                                        </p>
                                    </div>
                                    @if ($r->situacao !== '')
                                        <x-ui.badge :tone="$r->situacao === 'ATIVA' ? 'ok' : 'crit'" class="shrink-0">{{ $r->situacao }}</x-ui.badge>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ui.card>
        @endif

        <p class="text-[12px] text-ink-muted">Dados públicos da Receita Federal. CPF que aparece no nome de MEI é mascarado; não há busca por CPF.</p>
    </div>
</div>
