<div class="mx-auto max-w-[1200px] px-5 py-8 sm:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-ink">Organizações</h1>
        <p class="mt-1 text-sm text-ink-muted">Todas as organizações da plataforma e seu uso.</p>
    </div>

    @if (session('status'))
        <div class="mb-5 rounded-card border border-ok bg-ok-soft px-4 py-3 text-sm font-medium text-ok">
            {{ session('status') }}
        </div>
    @endif

    {{-- Observabilidade da manutenção mensal da base CNPJ --}}
    <div class="mb-6 rounded-card border border-line bg-surface p-5 shadow-card">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="text-ink-muted"><x-ui.icon name="database" :size="18" /></span>
                <h2 class="text-[15px] font-semibold text-ink">Base CNPJ — manutenção mensal</h2>
            </div>
            @if ($maintenanceLabel)
                <x-ui.badge :tone="$maintenanceTone" dot>{{ $maintenanceLabel }}</x-ui.badge>
            @endif
        </div>

        @if (! $lastMaintenance)
            <p class="mt-3 text-sm text-ink-muted">Nenhuma execução registrada ainda. A primeira roda no próximo ciclo mensal (dia 5) ou quando o script de manutenção for executado.</p>
        @else
            @if ($maintenanceStale)
                <div class="mt-3 rounded-card border border-high bg-high-soft px-4 py-3 text-sm font-medium text-high">
                    A última execução foi há mais de 10 dias — o agendamento pode ter parado. Verifique o cron do servidor.
                </div>
            @endif

            <div class="mt-3 text-sm">
                <span class="text-ink-muted">Último reimport:</span>
                <span class="font-medium text-ink">{{ $lastMaintenance->finished_at?->format('d/m/Y H:i') }}</span>
                <span class="text-ink-muted">({{ $lastMaintenance->finished_at?->diffForHumans() }})</span>
            </div>

            @php
                $stepMeta = [
                    'ok' => ['tone' => 'ok', 'icon' => 'check'],
                    'fail' => ['tone' => 'crit', 'icon' => 'close'],
                ];
                $steps = [
                    'Reimport' => $lastMaintenance->reimport_status,
                    'Re-coleta' => $lastMaintenance->recoleta_status,
                    'Normalizar sócios' => $lastMaintenance->normalizar_status,
                ];
            @endphp
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($steps as $stepLabel => $stepStatus)
                    @php $meta = $stepMeta[$stepStatus] ?? ['tone' => 'muted', 'icon' => 'remove']; @endphp
                    <x-ui.badge :tone="$meta['tone']" :icon="$meta['icon']">{{ $stepLabel }}</x-ui.badge>
                @endforeach
            </div>

            @if ($lastMaintenance->message)
                <p class="mt-3 rounded-card bg-surface-2 px-3 py-2 text-[13px] text-ink-2"><span class="font-semibold">Mensagem:</span> {{ $lastMaintenance->message }}</p>
            @endif

            @if ($maintenanceHistory->count() > 1)
                <details class="mt-4 text-sm">
                    <summary class="cursor-pointer select-none text-ink-muted hover:text-ink">Execuções anteriores</summary>
                    <ul class="mt-2 space-y-1.5">
                        @foreach ($maintenanceHistory->slice(1) as $run)
                            <li class="flex flex-wrap items-center gap-2 text-[13px]">
                                <span class="font-mono text-ink-muted">{{ $run->finished_at?->format('d/m/Y H:i') }}</span>
                                <x-ui.badge :tone="$run->isSuccess() ? 'ok' : 'crit'">{{ $run->isSuccess() ? 'ok' : 'falhou' }}</x-ui.badge>
                                @if ($run->message)<span class="text-ink-muted">· {{ $run->message }}</span>@endif
                            </li>
                        @endforeach
                    </ul>
                </details>
            @endif
        @endif
    </div>

    <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
        {{-- Lista --}}
        <div class="overflow-hidden rounded-card border border-line bg-surface shadow-card">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-line text-[11px] font-semibold uppercase tracking-wide text-ink-muted">
                        <th class="px-5 py-3">Organização</th>
                        <th class="px-3 py-3 text-center">Usuários</th>
                        <th class="px-3 py-3 text-center">Portfólios</th>
                        <th class="px-3 py-3 text-center">Empresas</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($organizations as $org)
                        <tr class="border-b border-line last:border-0 hover:bg-surface-2">
                            <td class="px-5 py-3.5">
                                <a href="{{ route('admin.organizations.show', $org) }}" wire:navigate class="font-semibold text-ink hover:underline">{{ $org->name }}</a>
                                <div class="mt-0.5 flex items-center gap-2 text-[12px] text-ink-muted">
                                    <span>criada {{ $org->created_at?->diffForHumans() }}</span>
                                    <x-ui.badge tone="muted">{{ $org->plan->label() }}</x-ui.badge>
                                    @if ($org->isSuspended())
                                        <x-ui.badge tone="crit" dot>Suspensa</x-ui.badge>
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-3.5 text-center font-mono text-[15px] text-ink-2">{{ $org->users_count }}</td>
                            <td class="px-3 py-3.5 text-center font-mono text-[15px] text-ink-2">{{ $org->portfolios_count }}</td>
                            <td class="px-3 py-3.5 text-center font-mono text-[15px] text-ink-2">{{ (int) ($companyCounts[$org->id] ?? 0) }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('admin.organizations.show', $org) }}" wire:navigate class="ui-icon-btn inline-flex"><x-ui.icon name="chevron_right" :size="18" /></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-sm text-ink-muted">Nenhuma organização ainda.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Onboardar nova organização --}}
        <div class="h-fit rounded-card border border-line bg-surface p-5 shadow-card">
            <h2 class="text-[15px] font-semibold text-ink">Nova organização</h2>
            <p class="mt-1 text-[12.5px] text-ink-muted">Cria a organização e o primeiro administrador dela.</p>

            <form wire:submit="createOrganization" class="mt-4 space-y-3">
                <div>
                    <label class="mb-1 block text-[12.5px] font-medium text-ink-2">Nome da organização</label>
                    <div class="ui-input-wrap"><input type="text" wire:model="orgName" class="ui-input" placeholder="Acme Ltda"></div>
                    @error('orgName') <span class="mt-1 block text-xs text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="border-t border-line pt-3">
                    <p class="mb-2 text-[11px] font-semibold uppercase tracking-wide text-ink-muted">Primeiro administrador</p>
                    <div class="space-y-3">
                        <div>
                            <label class="mb-1 block text-[12.5px] font-medium text-ink-2">Nome</label>
                            <div class="ui-input-wrap"><input type="text" wire:model="adminName" class="ui-input" placeholder="Maria Silva"></div>
                            @error('adminName') <span class="mt-1 block text-xs text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-[12.5px] font-medium text-ink-2">E-mail</label>
                            <div class="ui-input-wrap"><input type="email" wire:model="adminEmail" class="ui-input" placeholder="maria@acme.com"></div>
                            @error('adminEmail') <span class="mt-1 block text-xs text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-[12.5px] font-medium text-ink-2">Senha</label>
                            <div class="ui-input-wrap"><input type="text" wire:model="adminPassword" class="ui-input" placeholder="senha para repassar"></div>
                            @error('adminPassword') <span class="mt-1 block text-xs text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <x-ui.button type="submit" class="w-full justify-center">Criar organização</x-ui.button>
            </form>
        </div>
    </div>
</div>
