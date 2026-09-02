@php
    $counterColor = ['total' => 'text-ink', 'ok' => 'text-ok', 'not_found' => 'text-high', 'errors' => 'text-crit', 'pending' => 'text-high'];
@endphp

<div class="py-8">
    <div class="mx-auto max-w-[1200px] space-y-5 px-5 sm:px-8">
        <div>
            <a href="{{ route('portfolios.index') }}" wire:navigate class="mb-2 inline-flex items-center gap-1 text-[13px] font-medium text-ink-muted hover:text-ink-2">
                <x-ui.icon name="arrow_back" :size="17" />Portfólios
            </a>
            <div class="flex flex-wrap items-end justify-between gap-4">
                <h1 class="text-[26px] font-bold tracking-[-0.03em] text-ink">{{ $portfolio->name }}</h1>
                <x-ui.button wire:click="queueRefreshAll" wire:confirm="Enfileirar atualização de TODAS as empresas deste portfólio?" icon="bolt">
                    Atualizar todas
                </x-ui.button>
            </div>
        </div>

        @if (session('status'))
            <div class="flex items-center gap-2 rounded-btn border border-line bg-surface px-4 py-3 text-sm text-ink-2 shadow-card">
                <x-ui.icon name="check_circle" :size="18" class="text-ok" />{{ session('status') }}
            </div>
        @endif

        {{-- Progresso ao vivo --}}
        <div wire:poll.6s class="rounded-card border border-line bg-surface p-[22px] shadow-card">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-[15px] font-semibold text-ink">Progresso do monitoramento</h2>
                <span class="font-mono text-xs text-ink-muted">{{ $stats['queued'] }} job(s) na fila global</span>
            </div>
            @php $done = $stats['ok'] + $stats['not_found'] + $stats['errors']; $pct = $stats['total'] > 0 ? round($done / $stats['total'] * 100) : 0; @endphp
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-5">
                @foreach (['total' => 'Total', 'ok' => 'Atualizadas', 'not_found' => 'Não encontradas', 'errors' => 'Erros', 'pending' => 'Pendentes'] as $k => $lbl)
                    <div>
                        <div class="text-[11.5px] font-semibold uppercase tracking-wide text-ink-muted">{{ $lbl }}</div>
                        <div class="mt-1 text-[30px] font-bold tracking-[-0.03em] {{ $counterColor[$k] }}">{{ $stats[$k] }}</div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 h-2 w-full overflow-hidden rounded-full bg-surface-3">
                <div class="h-full rounded-full transition-all" style="width: {{ $pct }}%; background: linear-gradient(90deg, var(--primary), var(--hl));"></div>
            </div>
        </div>

        {{-- Adicionar empresa + Importar CSV --}}
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <form wire:submit="addCompany" class="space-y-3.5 rounded-card border border-line bg-surface p-[22px] shadow-card">
                <div class="flex items-center gap-2"><x-ui.icon name="add_business" :size="20" class="text-accent" /><h2 class="text-[15px] font-semibold text-ink">Adicionar empresa</h2></div>
                <div>
                    <label for="cnpj" class="mb-1.5 block text-[13px] font-semibold text-ink">CNPJ</label>
                    <div class="ui-input-wrap"><x-ui.icon name="badge" :size="19" class="text-ink-muted" /><input wire:model="cnpj" id="cnpj" type="text" placeholder="00.000.000/0001-91" class="ui-input" /></div>
                    @error('cnpj') <p class="mt-1 text-sm text-crit">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="label" class="mb-1.5 block text-[13px] font-semibold text-ink">Rótulo (opcional)</label>
                    <div class="ui-input-wrap"><x-ui.icon name="sell" :size="19" class="text-ink-muted" /><input wire:model="label" id="label" type="text" placeholder="Ex.: Fornecedor principal" class="ui-input" /></div>
                </div>
                <x-ui.button type="submit" icon="add">Adicionar</x-ui.button>
            </form>

            <form wire:submit="import" class="space-y-3.5 rounded-card border border-line bg-surface p-[22px] shadow-card">
                <div class="flex items-center gap-2"><x-ui.icon name="upload_file" :size="20" class="text-accent" /><h2 class="text-[15px] font-semibold text-ink">Importar CSV</h2></div>
                <p class="text-[13.5px] text-ink-2">Colunas: <code class="rounded bg-surface-2 px-1.5 py-0.5 font-mono text-[12.5px] text-ink-2 ring-1 ring-line">cnpj</code>, <code class="rounded bg-surface-2 px-1.5 py-0.5 font-mono text-[12.5px] text-ink-2 ring-1 ring-line">label</code> (opcional).</p>
                <input wire:model="csv" type="file" accept=".csv,.txt"
                       class="block w-full text-sm text-ink-2 file:mr-4 file:cursor-pointer file:rounded-btn file:border-0 file:bg-surface-2 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-ink hover:file:bg-surface-3" />
                @error('csv') <p class="mt-1 text-sm text-crit">{{ $message }}</p> @enderror
                <div wire:loading wire:target="csv" class="text-sm text-ink-muted">Enviando arquivo…</div>
                <x-ui.button type="submit" variant="secondary" icon="file_upload">Importar</x-ui.button>

                @if ($importReport !== null)
                    <div class="mt-2 rounded-btn border border-line bg-surface-2 p-3 text-sm">
                        <p class="font-semibold text-ink">{{ $importReport['imported'] }} adicionada(s), {{ count($importReport['rejected']) }} rejeitada(s).</p>
                        @if (count($importReport['rejected']) > 0)
                            <ul class="mt-2 max-h-40 space-y-1 overflow-y-auto text-ink-muted">
                                @foreach ($importReport['rejected'] as $row)
                                    <li>Linha {{ $row['line'] }}: <span class="font-mono">{{ $row['value'] ?: '(vazio)' }}</span> — {{ $row['reason'] }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif
            </form>
        </div>

        {{-- Filtros por status --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap gap-2">
                @foreach ($filters as $value => $f)
                    <button wire:click="setStatusFilter('{{ $value }}')"
                            @class([
                                'inline-flex items-center gap-2 rounded-full px-3.5 py-2 text-[13px] font-semibold transition-colors border cursor-pointer',
                                'bg-primary text-onprimary border-primary' => $statusFilter === $value,
                                'bg-surface text-ink-2 border-line-strong hover:bg-surface-2' => $statusFilter !== $value,
                            ])>
                        {{ $f['label'] }}
                        <span @class([
                            'rounded-full px-1.5 text-[11.5px]',
                            'bg-white/20 text-onprimary' => $statusFilter === $value,
                            'bg-surface-3 text-ink-muted' => $statusFilter !== $value,
                        ])>{{ $f['count'] }}</span>
                    </button>
                @endforeach
            </div>
            @if ($statusFilter !== 'all')
                <x-ui.button wire:click="reprocessFiltered" wire:confirm="Revalidar as {{ $filters[$statusFilter]['count'] }} empresa(s) do filtro '{{ $filters[$statusFilter]['label'] }}'?"
                             variant="secondary" size="sm" icon="refresh">
                    Revalidar filtrados ({{ $filters[$statusFilter]['count'] }})
                </x-ui.button>
            @endif
        </div>

        {{-- Tabela de empresas --}}
        <x-ui.card>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left">
                    <thead>
                        <tr class="border-b border-line bg-surface-2 text-[11.5px] font-semibold uppercase tracking-wide text-ink-muted">
                            <th class="px-[22px] py-3.5">Empresa</th>
                            <th class="px-4 py-3.5">Situação</th>
                            <th class="px-4 py-3.5">Status da coleta</th>
                            <th class="px-4 py-3.5">Atualizado</th>
                            <th class="px-[22px] py-3.5 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($companies as $company)
                            <tr class="border-b border-line last:border-b-0">
                                <td class="px-[22px] py-3.5">
                                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                        <a href="{{ route('companies.show', $company) }}" wire:navigate class="text-sm font-semibold text-accent hover:underline">
                                            {{ $company->latestSnapshot?->razao_social ?? $company->label ?? 'Sem dados ainda' }}
                                        </a>
                                        @if ($company->label !== null && $company->latestSnapshot?->razao_social !== null)
                                            <span class="inline-flex items-center gap-1 rounded-md bg-surface-2 px-2 py-0.5 text-xs font-medium text-ink-2 ring-1 ring-inset ring-line" title="Seu rótulo para esta unidade">
                                                <x-ui.icon name="sell" :size="13" class="text-ink-muted" />{{ $company->label }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mt-0.5 font-mono text-xs text-ink-muted">{{ $company->formattedCnpj() }}</div>
                                </td>
                                <td class="px-4 py-3.5 text-[13.5px] text-ink-2">{{ $company->latestSnapshot?->situacao_cadastral ?? '—' }}</td>
                                <td class="px-4 py-3.5">
                                    @if ($company->last_refresh_status === null)
                                        <x-ui.badge tone="high" icon="schedule">Pendente</x-ui.badge>
                                    @else
                                        <x-ui.badge :tone="$company->last_refresh_status->tone()" :icon="$company->last_refresh_status->icon()">{{ $company->last_refresh_status->label() }}</x-ui.badge>
                                        @if ($company->last_refresh_error)
                                            <p class="mt-1 max-w-xs truncate text-xs text-crit" title="{{ $company->last_refresh_error }}">{{ $company->last_refresh_error }}</p>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-[13px] text-ink-muted">{{ $company->last_refreshed_at?->diffForHumans() ?? 'Nunca' }}</td>
                                <td class="px-[22px] py-3.5">
                                    <div class="flex justify-end gap-1.5">
                                        <button wire:click="queueRefresh({{ $company->id }})" title="Atualizar" class="ui-icon-btn"><x-ui.icon name="refresh" :size="18" /></button>
                                        <button wire:click="removeCompany({{ $company->id }})" wire:confirm="Remover esta empresa do monitoramento?" title="Remover"
                                                class="flex h-9 w-9 items-center justify-center rounded-[9px] border border-crit bg-transparent text-crit transition-colors hover:bg-crit hover:text-crit-soft cursor-pointer"><x-ui.icon name="delete" :size="18" /></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-[22px] py-10 text-center text-sm text-ink-muted">
                                    {{ $statusFilter === 'all' ? 'Nenhuma empresa neste portfólio ainda.' : 'Nenhuma empresa neste filtro.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        <div>{{ $companies->links() }}</div>

        {{-- Agendamento automático + histórico --}}
        @php
            $todayDom = now()->day;
            $sel = $scheduleDays;
            $presets = [
                ['key' => 'day1', 'label' => 'Todo dia 1', 'days' => [1]],
                ['key' => 'day15', 'label' => 'Todo dia 15', 'days' => [15]],
                ['key' => 'biweekly', 'label' => 'Dias 1 e 15', 'days' => [1, 15]],
                ['key' => 'lastday', 'label' => 'Último dia', 'days' => [31]],
            ];
            $dayPhrase = function (array $days): string {
                $labels = array_map(fn ($d) => (string) $d, $days);
                if (count($labels) === 1) {
                    return 'no dia '.$labels[0];
                }
                $last = array_pop($labels);
                return 'nos dias '.implode(', ', $labels).' e '.$last;
            };
        @endphp

        <x-ui.card>
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-[22px] py-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-[9px] bg-primary-soft text-accent"><x-ui.icon name="event_repeat" :size="20" /></span>
                    <div>
                        <h2 class="text-[15px] font-semibold text-ink">Agendamento automático</h2>
                        <p class="text-[13px] text-ink-muted">
                            @if (count($sel) > 0)
                                Revalida os CNPJs sozinho, todo mês {{ $dayPhrase($sel) }}.
                            @else
                                Defina quando este portfólio deve revalidar os CNPJs sozinho.
                            @endif
                        </p>
                    </div>
                </div>
                @if (count($sel) > 0)
                    <x-ui.badge tone="ok" dot>Ativo</x-ui.badge>
                @else
                    <x-ui.badge tone="muted" dot>Inativo</x-ui.badge>
                @endif
            </div>

            <div class="grid grid-cols-1 gap-6 p-[22px] lg:grid-cols-[minmax(0,1fr)_20rem]">
                {{-- Calendário de dias --}}
                <div>
                    <div class="mb-3 flex items-center justify-between">
                        <p class="text-[11.5px] font-semibold uppercase tracking-wide text-ink-muted">Dias do mês</p>
                        @if (count($sel) > 0)
                            <button type="button" wire:click="clearScheduleDays" class="inline-flex cursor-pointer items-center gap-1 text-xs font-medium text-ink-muted transition-colors hover:text-crit">
                                <x-ui.icon name="close" :size="14" />Limpar
                            </button>
                        @endif
                    </div>
                    <div class="grid grid-cols-7 gap-1.5" role="group" aria-label="Dias do mês para agendamento">
                        @for ($d = 1; $d <= 31; $d++)
                            @php $on = in_array($d, $sel, true); @endphp
                            <button type="button" wire:click="toggleScheduleDay({{ $d }})"
                                    aria-pressed="{{ $on ? 'true' : 'false' }}" aria-label="Dia {{ $d }}{{ $d === $todayDom ? ' — hoje' : '' }}"
                                    @class([
                                        'relative flex h-10 items-center justify-center rounded-[10px] text-sm font-semibold cursor-pointer transition-colors border focus:outline-none focus-visible:ring-2 focus-visible:ring-focus',
                                        'bg-primary text-onprimary border-primary hover:bg-primary-hover' => $on,
                                        'bg-surface text-ink-2 border-line-strong hover:bg-surface-2' => ! $on,
                                        'ring-2 ring-hl' => $d === $todayDom && ! $on,
                                    ])>
                                {{ $d }}
                                @if ($d === $todayDom)<span class="absolute bottom-1 h-1 w-1 rounded-full {{ $on ? 'bg-white/80' : 'bg-hl' }}"></span>@endif
                            </button>
                        @endfor
                    </div>
                    <p class="mt-3 flex items-start gap-2 text-xs text-ink-muted">
                        <span class="mt-1 inline-block h-1.5 w-1.5 shrink-0 rounded-full bg-hl"></span>
                        Hoje é dia {{ $todayDom }}. Dias 29–31 rodam no último dia do mês quando ele não existe (ex.: 31 cai em 28/fev).
                    </p>
                </div>

                {{-- Atalhos + próxima execução + salvar --}}
                <div class="flex flex-col gap-4 lg:border-l lg:border-line lg:pl-6">
                    <div>
                        <p class="mb-2 text-[11.5px] font-semibold uppercase tracking-wide text-ink-muted">Atalhos</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($presets as $preset)
                                @php $active = $sel === $preset['days']; @endphp
                                <button type="button" wire:click="applyPreset('{{ $preset['key'] }}')"
                                        @class([
                                            'rounded-full px-3.5 py-2 text-[13px] font-semibold cursor-pointer transition-colors border focus:outline-none focus-visible:ring-2 focus-visible:ring-focus',
                                            'bg-primary text-onprimary border-primary' => $active,
                                            'bg-surface text-ink-2 border-line-strong hover:bg-surface-2' => ! $active,
                                        ])>{{ $preset['label'] }}</button>
                            @endforeach
                        </div>
                    </div>

                    @if ($nextRun !== null)
                        @php $diasAte = (int) now()->startOfDay()->diffInDays($nextRun); @endphp
                        <div class="rounded-btn border border-hl-soft bg-hl-soft p-3">
                            <div class="flex items-center gap-1.5 text-hl-ink"><x-ui.icon name="schedule" :size="16" /><span class="text-[11.5px] font-semibold uppercase tracking-wide">Próxima execução</span></div>
                            <p class="mt-1 text-sm font-semibold text-hl-ink">{{ $nextRun->format('d/m/Y') }}</p>
                            <p class="text-xs text-hl-ink/70">
                                @if ($diasAte === 0) hoje, às 3h da manhã
                                @elseif ($diasAte === 1) amanhã, às 3h da manhã
                                @else em {{ $diasAte }} dias @endif
                            </p>
                        </div>
                    @else
                        <div class="rounded-btn border border-line bg-surface-2 p-3">
                            <p class="text-sm text-ink-muted">Sem execução agendada. As coletas só rodam quando você clica em <span class="font-medium text-ink-2">Atualizar</span>.</p>
                        </div>
                    @endif

                    <div class="mt-auto">
                        @if ($scheduleDirty)
                            <div class="flex flex-col gap-2">
                                <p class="flex items-center gap-1.5 text-xs font-semibold text-high"><span class="h-1.5 w-1.5 rounded-full bg-high"></span>Alterações não salvas</p>
                                <div class="flex items-center gap-2">
                                    <button wire:click="saveSchedule" wire:loading.attr="disabled" wire:target="saveSchedule"
                                            class="inline-flex flex-1 cursor-pointer items-center justify-center gap-1.5 rounded-btn bg-primary px-4 py-2.5 text-sm font-semibold text-onprimary transition-colors hover:bg-primary-hover disabled:opacity-60">
                                        <span wire:loading.remove wire:target="saveSchedule">Salvar agendamento</span>
                                        <span wire:loading wire:target="saveSchedule">Salvando…</span>
                                    </button>
                                    <button wire:click="discardScheduleChanges" class="cursor-pointer rounded-btn px-3 py-2.5 text-sm font-medium text-ink-muted transition-colors hover:bg-surface-2 hover:text-ink-2">Descartar</button>
                                </div>
                            </div>
                        @else
                            <p class="flex items-center gap-1.5 text-sm {{ count($sel) > 0 ? 'text-ok' : 'text-ink-muted' }}">
                                <x-ui.icon name="check" :size="16" />{{ count($sel) > 0 ? 'Agendamento salvo' : 'Nenhum agendamento' }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </x-ui.card>

        {{-- Histórico de execuções --}}
        <x-ui.card>
            <div class="flex items-center justify-between border-b border-line px-[22px] py-4">
                <h2 class="text-[15px] font-semibold text-ink">Histórico de execuções</h2>
                @if ($scheduledRuns->isNotEmpty())<span class="font-mono text-xs text-ink-muted">últimas {{ $scheduledRuns->count() }}</span>@endif
            </div>
            @forelse ($scheduledRuns as $run)
                <div class="flex items-center justify-between border-b border-line px-[22px] py-3.5 text-sm last:border-b-0">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-[9px] bg-ok-soft text-ok"><x-ui.icon name="check" :size="17" /></span>
                        <div>
                            <span class="font-semibold text-ink">{{ $run->ran_on->format('d/m/Y') }}</span>
                            <span class="text-ink-muted">· agendado p/ dia {{ $run->scheduled_day }}</span>
                        </div>
                    </div>
                    <span class="text-ink-2">{{ $run->companies_count }} empresa(s)</span>
                </div>
            @empty
                <div class="px-[22px] py-10 text-center text-sm text-ink-muted">Nenhuma execução automática ainda. Quando um dia agendado chegar, ela aparece aqui.</div>
            @endforelse
        </x-ui.card>
    </div>
</div>
