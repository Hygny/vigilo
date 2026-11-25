<div class="py-8">
    <div class="mx-auto max-w-[1000px] space-y-5 px-5 sm:px-8">
        <a href="{{ route('pessoas.busca') }}" wire:navigate class="inline-flex items-center gap-1 text-[13px] font-medium text-ink-muted hover:text-ink-2">
            <x-ui.icon name="arrow_back" :size="17" />Voltar à busca de pessoas
        </a>

        @if ($limitado)
            <div class="flex items-start gap-2 rounded-btn border border-line bg-surface px-4 py-3 text-sm text-ink-2 shadow-card">
                <x-ui.icon name="hourglass_top" :size="18" class="mt-0.5 text-ink-muted" />Muitos perfis abertos em pouco tempo. Aguarde um minuto e recarregue a página.
            </div>
        @elseif ($aviso)
            <div class="flex items-start gap-2 rounded-btn border border-line bg-surface px-4 py-3 text-sm text-ink-2 shadow-card">
                <x-ui.icon name="cloud_off" :size="18" class="mt-0.5 text-ink-muted" />{{ $aviso }}
            </div>
        @elseif ($pessoa === null)
            <x-ui.card class="p-[22px]">
                <h1 class="text-[20px] font-bold text-ink">Pessoa não encontrada</h1>
                <p class="mt-1 text-sm text-ink-muted">O quadro societário pode ter mudado desde a última atualização da base. Faça a busca de novo.</p>
            </x-ui.card>
        @else
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-[26px] font-bold tracking-[-0.03em] text-ink">{{ $pessoa->nome }}</h1>
                    <x-ui.badge tone="med">Vínculo provável</x-ui.badge>
                </div>
                <p class="mt-1 font-mono text-sm text-ink-muted">{{ $pessoa->documento ?? 'sem documento' }}@if ($pessoa->estrangeiro) · estrangeiro @endif · sócio(a) de {{ $pessoa->totalEmpresas }} empresa(s)</p>
            </div>

            <div class="flex items-start gap-2 rounded-btn border border-line bg-surface-2 px-4 py-3 text-[13px] text-ink-2">
                <x-ui.icon name="info" :size="18" class="mt-0.5 shrink-0 text-ink-muted" />
                <p>Empresas em que aparece um sócio com <span class="font-medium">este nome e este CPF mascarado</span>. A Receita não publica o CPF inteiro: pessoas diferentes com o mesmo nome e os mesmos 6 dígitos do meio aparecem aqui como uma só. Trate como vínculo provável e confirme por outra fonte antes de concluir.</p>
            </div>

            <x-ui.card class="p-[22px]">
                <div class="mb-3 flex items-center gap-2"><x-ui.icon name="domain" :size="20" class="text-accent" /><h2 class="text-[15px] font-semibold text-ink">Empresas</h2></div>
                @if ($pessoa->empresas === [])
                    <p class="text-sm text-ink-muted">Nenhuma empresa encontrada agora — o quadro societário pode ter mudado na última atualização da base.</p>
                @else
                    <ul class="divide-y divide-line">
                        @foreach ($pessoa->empresas as $e)
                            <li class="flex flex-wrap items-center justify-between gap-2 py-2.5 text-sm">
                                <span class="flex min-w-0 items-center gap-2">
                                    <a href="{{ route('empresas.perfil', $e->cnpj) }}" wire:navigate class="truncate font-medium text-ink hover:underline">{{ $e->nome }}</a>
                                    @if ($e->situacao && $e->situacao !== 'ATIVA')<x-ui.badge tone="crit">{{ $e->situacao }}</x-ui.badge>@endif
                                </span>
                                <span class="text-ink-muted">{{ collect([$e->qualificacao, $e->dataEntrada ? 'desde '.\Illuminate\Support\Carbon::parse($e->dataEntrada)->format('d/m/Y') : null, $e->uf])->filter()->implode(' · ') }}</span>
                            </li>
                        @endforeach
                    </ul>
                    @if ($pessoa->totalEmpresas > count($pessoa->empresas))
                        <p class="mt-3 text-[12px] text-ink-muted">Mostrando {{ count($pessoa->empresas) }} de {{ $pessoa->totalEmpresas }} (ativas primeiro).</p>
                    @endif
                @endif
            </x-ui.card>

            <x-ui.card class="p-[22px]">
                <div class="mb-3 flex items-center gap-2"><x-ui.icon name="diversity_3" :size="20" class="text-accent" /><h2 class="text-[15px] font-semibold text-ink">Homônimos</h2><span class="text-[12.5px] text-ink-muted">· mesmo nome, outro CPF mascarado</span></div>
                @if ($pessoa->homonimos === [])
                    <p class="text-sm text-ink-muted">Nenhum outro sócio com este nome exato.</p>
                @else
                    <ul class="divide-y divide-line">
                        @foreach ($pessoa->homonimos as $h)
                            <li>
                                <a href="{{ route('pessoas.perfil', $h->chave) }}" wire:navigate class="-mx-2 flex items-center justify-between gap-3 rounded-btn px-2 py-2.5 text-sm hover:bg-surface-2">
                                    <span class="font-mono text-ink-2">{{ $h->documento ?? 'sem documento' }}</span>
                                    <span class="text-ink-muted">{{ $h->empresas }} empresa(s)</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <p class="mt-3 text-[12px] text-ink-muted">Pessoas diferentes para a Receita (outro CPF). Mostrando até {{ $maxHomonimos }}.</p>
                @endif
            </x-ui.card>
        @endif
    </div>
</div>
