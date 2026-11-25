{{-- Risco ao redor: empresas ligadas por relação certa com sinal de risco.
     Espera $aoRedor (RiscoAoRedorReport|null). --}}
<div>
    <div class="mb-2 flex items-center gap-2">
        <x-ui.icon name="hub" :size="18" class="text-accent" />
        <h3 class="text-[13.5px] font-semibold text-ink">Risco ao redor</h3>
        <span class="text-[12px] text-ink-muted">· empresas ligadas por sócio PJ, participação ou grupo</span>
    </div>
    @if ($aoRedor === null)
        <p class="text-sm text-ink-muted">Indisponível no momento.</p>
    @elseif ($aoRedor->total() === 0)
        <p class="text-sm text-ink-muted">Nenhuma empresa ligada por relação certa (sócio PJ, participação ou grupo econômico).</p>
    @else
        @php($comAlerta = $aoRedor->comAlerta())
        @if ($comAlerta === [] && $aoRedor->semSinalConfiavel())
            <div class="flex items-start gap-2 rounded-btn bg-ok-soft px-4 py-3 text-sm text-ok">
                <x-ui.icon name="verified_user" :size="18" class="mt-0.5" />
                <p>Nenhuma das {{ $aoRedor->total() }} empresa(s) ligada(s) tem sinal de risco nas fontes consultadas.</p>
            </div>
        @elseif ($comAlerta === [])
            {{-- Alguma fonte ficou de fora ou a cópia da CGU está velha: sem verde, sem "garantia". --}}
            <div class="flex items-start gap-2 rounded-btn border border-line px-4 py-3 text-sm text-ink-2">
                <x-ui.icon name="info" :size="18" class="mt-0.5 text-ink-muted" />
                <p>Nenhum sinal de risco entre as {{ $aoRedor->total() }} empresa(s) ligada(s) nas fontes que puderam ser consultadas.</p>
            </div>
        @else
            <div class="flex items-start gap-2 rounded-btn bg-high-soft px-4 py-3 text-sm text-high">
                <x-ui.icon name="warning" :size="18" class="mt-0.5" />
                <p class="font-semibold">{{ $aoRedor->selo() }}</p>
            </div>
            <ul class="mt-2 divide-y divide-line">
                @foreach ($comAlerta as $ligada)
                    <li class="flex flex-wrap items-center justify-between gap-2 py-2 text-sm">
                        <span class="flex min-w-0 items-center gap-2">
                            <a href="{{ route('empresas.perfil', $ligada->cnpj) }}" wire:navigate class="truncate font-medium text-ink hover:underline">{{ $ligada->nome }}</a>
                            <span class="shrink-0 rounded-md bg-surface-2 px-1.5 py-0.5 text-[11px] text-ink-muted ring-1 ring-inset ring-line">{{ $ligada->ligacao }}</span>
                        </span>
                        <span class="text-[12.5px] text-danger">{{ implode(' · ', $ligada->sinais()) }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
        @if ($aoRedor->naoConsultadas() !== [])
            <p class="mt-2 text-[12px] text-ink-muted">Não consultado agora: {{ implode(' e ', $aoRedor->naoConsultadas()) }}.</p>
        @endif
        @if ($aoRedor->sancoesDesatualizadas)
            <p class="mt-2 text-[12px] font-semibold text-high">A cópia das sanções da CGU está desatualizada — sanção recente pode não aparecer.</p>
        @endif
    @endif
</div>
