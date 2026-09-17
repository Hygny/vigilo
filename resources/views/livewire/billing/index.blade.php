@php
    use App\Enums\BillingStatus;

    $statusTone = match ($billingStatus) {
        BillingStatus::Active => 'ok',
        BillingStatus::Trialing => 'high',
        BillingStatus::PastDue => 'crit',
        default => 'muted',
    };

    $paymentLabel = fn (string $status): string => match ($status) {
        'PENDING', 'AWAITING_RISK_ANALYSIS' => 'Pendente',
        'CONFIRMED', 'RECEIVED', 'RECEIVED_IN_CASH' => 'Pago',
        'OVERDUE' => 'Vencido',
        'REFUNDED', 'REFUND_REQUESTED' => 'Estornado',
        'CANCELED', 'DELETED' => 'Cancelado',
        default => $status,
    };
@endphp

<div class="py-8">
    <div class="mx-auto max-w-[860px] space-y-6 px-5 sm:px-8">
        <div>
            <h1 class="text-[28px] font-bold tracking-[-0.03em] text-ink">Assinatura</h1>
            <p class="mt-1 text-[14.5px] text-ink-muted">Plano, cobrança e faturas da sua organização.</p>
        </div>

        @if (session('status'))
            <div class="flex items-center gap-2 rounded-btn border border-line bg-surface px-4 py-3 text-sm text-ink-2 shadow-card">
                <x-ui.icon name="check_circle" :size="18" class="text-ok" />{{ session('status') }}
            </div>
        @endif

        {{-- Plano atual --}}
        <div class="space-y-4 rounded-[14px] border border-line bg-surface p-5 shadow-card sm:p-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-[12.5px] font-semibold uppercase tracking-wide text-ink-muted">Plano atual</p>
                    <p class="mt-1 text-[24px] font-bold tracking-tight text-ink">{{ $plan->label() }}</p>
                    <p class="mt-1 text-[14px] text-ink-2">
                        @if ($plan->monthlyPrice() > 0)
                            R$ {{ number_format($plan->monthlyPrice(), 2, ',', '.') }} <span class="text-ink-muted">/ mês</span>
                        @else
                            <span class="text-ink-muted">Gratuito</span>
                        @endif
                        &nbsp;·&nbsp; até {{ number_format($organization->maxMonitoredCompanies(), 0, ',', '.') }} CNPJs
                        ({{ $organization->monitoredCompaniesCount() }} em uso)
                    </p>
                </div>
                <x-ui.badge :tone="$statusTone" dot>{{ $billingStatus->label() }}</x-ui.badge>
            </div>

            @if ($nextDueDate || $invoiceUrl)
                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-line pt-4">
                    @if ($nextDueDate)
                        <p class="text-[14px] text-ink-2">
                            Próxima fatura:
                            <strong class="text-ink">{{ \Illuminate\Support\Carbon::parse($nextDueDate)->format('d/m/Y') }}</strong>
                        </p>
                    @else
                        <span></span>
                    @endif

                    @if ($invoiceUrl)
                        <x-ui.button :href="$invoiceUrl" size="sm" icon="open_in_new" target="_blank" rel="noopener">
                            Pagar fatura em aberto
                        </x-ui.button>
                    @endif
                </div>
            @endif
        </div>

        {{-- Avisos de integração / estado --}}
        @if (! $organization->hasSubscription())
            <div class="flex items-start gap-3 rounded-[14px] border border-line bg-surface-2 p-5 text-[14px] text-ink-2">
                <x-ui.icon name="info" :size="20" class="mt-0.5 shrink-0 text-ink-muted" />
                <p>Você está no plano <strong>{{ $plan->label() }}</strong>. Para aumentar o limite de CNPJs monitorados, fale com o suporte para migrar para <strong>Pro</strong> ou <strong>Business</strong>.</p>
            </div>
        @elseif (! $integrationAvailable)
            <div class="flex items-start gap-3 rounded-[14px] border border-line bg-surface-2 p-5 text-[14px] text-ink-2">
                <x-ui.icon name="info" :size="20" class="mt-0.5 shrink-0 text-ink-muted" />
                <p>A cobrança online ainda não está configurada nesta instância. Fale com o suporte para acertar o pagamento.</p>
            </div>
        @elseif ($integrationDown)
            <div class="flex items-start gap-3 rounded-[14px] border border-line bg-surface-2 p-5 text-[14px] text-ink-2">
                <x-ui.icon name="warning" :size="20" class="mt-0.5 shrink-0 text-high" />
                <p>Não foi possível carregar os dados de cobrança agora. Tente novamente em instantes.</p>
            </div>
        @endif

        {{-- Histórico de cobranças --}}
        @if (count($payments) > 0)
            <div class="overflow-hidden rounded-[14px] border border-line bg-surface shadow-card">
                <div class="border-b border-line px-5 py-3.5">
                    <h2 class="text-[15px] font-semibold text-ink">Histórico de cobranças</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[14px]">
                        <thead>
                            <tr class="border-b border-line text-[12.5px] uppercase tracking-wide text-ink-muted">
                                <th class="px-5 py-3 font-semibold">Vencimento</th>
                                <th class="px-5 py-3 font-semibold">Valor</th>
                                <th class="px-5 py-3 font-semibold">Situação</th>
                                <th class="px-5 py-3 font-semibold text-right">Fatura</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr class="border-b border-line last:border-0">
                                    <td class="px-5 py-3 text-ink-2">
                                        {{ $payment['dueDate'] ? \Illuminate\Support\Carbon::parse($payment['dueDate'])->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="px-5 py-3 text-ink">R$ {{ number_format($payment['value'], 2, ',', '.') }}</td>
                                    <td class="px-5 py-3 text-ink-2">{{ $paymentLabel($payment['status']) }}</td>
                                    <td class="px-5 py-3 text-right">
                                        @if ($payment['invoiceUrl'])
                                            <a href="{{ $payment['invoiceUrl'] }}" target="_blank" rel="noopener"
                                               class="inline-flex items-center gap-1 text-[13.5px] font-semibold text-accent hover:underline">
                                                Ver <x-ui.icon name="open_in_new" :size="15" />
                                            </a>
                                        @else
                                            <span class="text-ink-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
