{{-- Recuperação de crédito: monitorar a empresa como devedora. Espera $company e $contagem. --}}
@php($M = \App\Services\Bens\DevedorMonitor::class)
<div>
    <x-ui.card class="p-[22px]">
        <div class="mb-3 flex flex-wrap items-center gap-2">
            <x-ui.icon name="crisis_alert" :size="20" class="text-accent" />
            <h2 class="text-[15px] font-semibold text-ink">Recuperação de crédito</h2>
            @if ($company->emRecuperacao())
                <x-ui.badge tone="high" dot>Devedora monitorada</x-ui.badge>
            @endif
        </div>

        @if (session('status'))
            <p class="mb-3 text-[13px] text-ok">{{ session('status') }}</p>
        @endif

        @if ($company->emRecuperacao())
            <p class="text-sm text-ink-2">
                Monitorada desde {{ $company->recuperacao_desde->format('d/m/Y') }}.
                Avisamos (Inbox, e-mail e webhook) quando aparecer <span class="font-medium">aeronave, participação societária, contrato federal vigente</span> ou <span class="font-medium">possível sucessora</span> que ela ainda não tinha.
            </p>
            <p class="mt-1 text-[12.5px] text-ink-muted">
                @if ($company->recuperacao_synced_at)
                    Última checagem em {{ $company->recuperacao_synced_at->timezone(config('risco.sweep.timezone'))->format('d/m/Y H:i') }} —
                    conhecidos: {{ $contagem[$M::AERONAVE] ?? 0 }} aeronave(s), {{ $contagem[$M::PARTICIPACAO] ?? 0 }} participação(ões), {{ $contagem[$M::CONTRATO] ?? 0 }} contrato(s) vigente(s), {{ $contagem[$M::SUCESSORA] ?? 0 }} possível(is) sucessora(s).
                @else
                    Registrando o que ela já tem (sem alertas): os avisos começam com o que surgir a partir de agora.
                @endif
                Checagem {{ \App\Console\Commands\SweepDevedores::HORARIOS }}.
            </p>
        @else
            <p class="text-sm text-ink-2">Marque como devedora para ser avisado quando ela ganhar <span class="font-medium">bens</span> (aeronaves, participações), <span class="font-medium">créditos a receber</span> (contratos federais) ou abrir uma <span class="font-medium">possível sucessora</span> — o que dá para penhorar ou redirecionar.</p>
        @endif

        <form wire:submit="ativar" class="mt-3 flex flex-wrap items-center gap-2">
            <label for="recuperacao-data-{{ $company->id }}" class="text-[12.5px] text-ink-muted">Data da dívida (opcional)</label>
            <input id="recuperacao-data-{{ $company->id }}" type="date" wire:model="data" min="1990-01-01" max="{{ now()->toDateString() }}" class="rounded-btn border border-line bg-surface px-2 py-1 text-sm text-ink" />
            @can('update', $company)
                <x-ui.button type="submit" size="sm" :variant="$company->emRecuperacao() ? 'secondary' : 'primary'">{{ $company->emRecuperacao() ? 'Salvar data' : 'Monitorar como devedora' }}</x-ui.button>
                @if ($company->emRecuperacao())
                    <button type="button" wire:click="desativar" wire:confirm="Parar de monitorar esta empresa como devedora? O que já foi registrado dela é apagado." class="text-[12.5px] font-medium text-ink-muted hover:text-danger">parar de monitorar</button>
                @endif
            @endcan
            @error('data') <p class="w-full text-[12.5px] text-danger">{{ $message }}</p> @enderror
        </form>
        <p class="mt-2 text-[12px] text-ink-muted">Sem data, o marco das possíveis sucessoras é a 1ª inscrição em dívida ativa da União.</p>
    </x-ui.card>
</div>
