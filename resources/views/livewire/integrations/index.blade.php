<div class="py-8">
    <div class="mx-auto max-w-[900px] space-y-6 px-5 sm:px-8">
        <div>
            <h1 class="text-[28px] font-bold tracking-[-0.03em] text-ink">Integrações</h1>
            <p class="mt-1 text-[14.5px] text-ink-muted">Receba os alertas da sua carteira em tempo real no seu sistema, via webhook assinado.</p>
        </div>

        @if (session('status'))
            <div class="flex items-center gap-2 rounded-btn border border-line bg-surface px-4 py-3 text-sm text-ink-2 shadow-card">
                <x-ui.icon name="info" :size="18" class="text-primary" />{{ session('status') }}
            </div>
        @endif

        {{-- Configuração do webhook --}}
        <div class="rounded-card border border-line bg-surface p-5 shadow-card sm:p-6">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-ink-muted"><x-ui.icon name="webhook" :size="20" /></span>
                    <h2 class="text-[16px] font-semibold text-ink">Webhook de saída</h2>
                </div>
                @if ($configured)
                    <x-ui.badge tone="ok" dot>Ativo</x-ui.badge>
                @else
                    <x-ui.badge tone="muted">Não configurado</x-ui.badge>
                @endif
            </div>

            <p class="mt-2 text-[13.5px] text-ink-muted">
                A cada alerta detectado, enviamos um <span class="font-mono">POST</span> para a sua URL com o evento em JSON,
                assinado com HMAC-SHA256 no header <span class="font-mono">X-Vigilo-Signature</span>.
            </p>

            <form wire:submit="save" class="mt-5 space-y-4">
                <div>
                    <label class="mb-1 block text-[13px] font-medium text-ink-2">URL do endpoint</label>
                    <div class="ui-input-wrap"><input type="url" wire:model="webhookUrl" class="ui-input" placeholder="https://seu-sistema.com/webhooks/vigilo"></div>
                    @error('webhookUrl') <span class="mt-1 block text-xs text-danger">{{ $message }}</span> @enderror
                    <p class="mt-1 text-[12px] text-ink-muted">Deixe em branco e salve para desativar o webhook.</p>
                </div>

                <div>
                    <label class="mb-1 block text-[13px] font-medium text-ink-2">Segredo (para assinar)</label>
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <div class="ui-input-wrap flex-1"><input type="text" wire:model="webhookSecret" class="ui-input font-mono" placeholder="{{ $configured ? 'Configurado — deixe em branco para manter' : 'Clique em Gerar ou cole um segredo' }}"></div>
                        <x-ui.button type="button" variant="secondary" size="sm" icon="key" wire:click="generateSecret">Gerar</x-ui.button>
                    </div>
                    @error('webhookSecret') <span class="mt-1 block text-xs text-danger">{{ $message }}</span> @enderror
                    <p class="mt-1 text-[12px] text-ink-muted">Copie e guarde no seu sistema — é com ele que você valida a assinatura. Por segurança, não exibimos o segredo salvo.</p>
                </div>

                <div class="flex flex-wrap gap-2 border-t border-line pt-4">
                    <x-ui.button type="submit">Salvar</x-ui.button>
                    <x-ui.button type="button" variant="secondary" icon="send" wire:click="sendTest">Enviar teste</x-ui.button>
                </div>
            </form>
        </div>

        {{-- Como validar a assinatura --}}
        <details class="rounded-card border border-line bg-surface p-5 shadow-card">
            <summary class="cursor-pointer select-none text-[14px] font-semibold text-ink">Como validar a assinatura</summary>
            <div class="mt-3 space-y-2 text-[13px] text-ink-2">
                <p>Recompute o HMAC-SHA256 sobre o <strong>corpo cru</strong> da requisição, com o seu segredo, e compare com o header (use comparação de tempo constante):</p>
                <pre class="overflow-x-auto rounded-btn bg-surface-2 p-3 font-mono text-[12px] text-ink-2">assinatura = "sha256=" + hmac_sha256(corpo_cru, segredo)
confere? hash_equals(assinatura, header["X-Vigilo-Signature"])</pre>
                <p class="text-ink-muted">Cada entrega traz um <span class="font-mono">X-Vigilo-Event-Id</span> estável entre retentativas — use-o para deduplicar.</p>
            </div>
        </details>

        {{-- Últimas entregas --}}
        <div class="rounded-card border border-line bg-surface shadow-card">
            <div class="border-b border-line px-5 py-3.5">
                <h2 class="text-[15px] font-semibold text-ink">Últimas entregas</h2>
            </div>
            @forelse ($deliveries as $delivery)
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 border-b border-line px-5 py-3 last:border-0">
                    <span class="font-mono text-[12.5px] text-ink-muted">{{ $delivery->created_at?->format('d/m/Y H:i') }}</span>
                    <x-ui.badge :tone="$delivery->isSuccess() ? 'ok' : 'crit'">{{ $delivery->isSuccess() ? 'entregue' : 'falhou' }}</x-ui.badge>
                    <span class="text-[13px] text-ink-2">{{ $delivery->event_type }}</span>
                    @if ($delivery->cnpj)<span class="font-mono text-[12.5px] text-ink-muted">{{ $delivery->cnpj }}</span>@endif
                    @if ($delivery->response_status)<span class="text-[12.5px] text-ink-muted">HTTP {{ $delivery->response_status }}</span>@endif
                    @if ($delivery->error)<span class="text-[12.5px] text-danger">{{ $delivery->error }}</span>@endif
                </div>
            @empty
                <div class="px-5 py-10 text-center text-sm text-ink-muted">Nenhuma entrega ainda.</div>
            @endforelse
        </div>
    </div>
</div>
