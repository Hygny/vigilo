<div class="py-8">
    <div class="mx-auto max-w-[1000px] space-y-5 px-5 sm:px-8">
        <div>
            @include('partials.busca-abas', ['ativa' => 'pessoas'])
            <h1 class="mt-3 text-[26px] font-bold tracking-[-0.03em] text-ink">Busca de pessoas</h1>
            <p class="mt-1 text-sm text-ink-muted">Sócios (pessoa física ou estrangeiro) pelo nome, e as empresas de cada um — direto do quadro societário da Receita Federal.</p>
        </div>

        <form wire:submit="buscar" class="rounded-card border border-line bg-surface p-[22px] shadow-card">
            <div class="flex flex-col gap-3 sm:flex-row">
                <div class="ui-input-wrap flex-1">
                    <x-ui.icon name="person_search" :size="19" class="text-ink-muted" />
                    <input wire:model="termo" type="search" autofocus autocomplete="off" placeholder="Nome e sobrenome. Ex.: maria aparecida souza" class="ui-input" aria-label="Nome da pessoa" />
                </div>
                <x-ui.button type="submit" icon="search">Buscar</x-ui.button>
            </div>
        </form>

        <div class="flex items-start gap-2 rounded-btn border border-line bg-surface-2 px-4 py-3 text-[13px] text-ink-2">
            <x-ui.icon name="info" :size="18" class="mt-0.5 shrink-0 text-ink-muted" />
            <p>A Receita publica o CPF do sócio <span class="font-medium">mascarado</span> (só 6 dígitos do meio). Cada resultado é uma <span class="font-medium">pessoa provável</span>: mesmo nome e mesmo CPF mascarado. Homônimos com os mesmos 6 dígitos podem aparecer juntos — confirme antes de concluir. Toda busca fica registrada.</p>
        </div>

        @if ($aviso)
            <div class="flex items-start gap-2 rounded-btn border border-line bg-surface px-4 py-3 text-sm text-ink-2 shadow-card">
                <x-ui.icon name="info" :size="18" class="mt-0.5 text-ink-muted" />{{ $aviso }}
            </div>
        @endif

        @if ($resultados !== null)
            <x-ui.card class="p-[22px]">
                <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <x-ui.icon name="group" :size="20" class="text-accent" />
                        <h2 class="text-[15px] font-semibold text-ink">Pessoas</h2>
                        <span class="text-[12.5px] text-ink-muted">· {{ count($resultados->itens) }}</span>
                    </div>
                    @if ($resultados->truncado)
                        <span class="text-[12.5px] text-ink-muted">Muitas pessoas com esse nome — mostrando as mais parecidas. Acrescente outro sobrenome.</span>
                    @endif
                </div>

                @if ($resultados->itens === [])
                    <p class="text-sm text-ink-muted">Nenhum sócio encontrado com esse nome.</p>
                @else
                    <ul class="divide-y divide-line">
                        @foreach ($resultados->itens as $p)
                            <li>
                                <a href="{{ route('pessoas.perfil', $p->chave) }}" wire:navigate class="-mx-2 flex items-center justify-between gap-3 rounded-btn px-2 py-3 hover:bg-surface-2">
                                    <div class="min-w-0">
                                        <p class="font-medium text-ink">{{ $p->nome }}</p>
                                        <p class="mt-0.5 font-mono text-[12.5px] text-ink-muted">{{ $p->documento ?? 'sem documento' }}@if ($p->estrangeiro) · estrangeiro @endif</p>
                                    </div>
                                    <span class="shrink-0 text-[13px] text-ink-2">{{ $p->empresas }} empresa(s)</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ui.card>
        @endif
    </div>
</div>
