<div class="py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <a href="{{ route('portfolios.index') }}" wire:navigate class="text-sm text-slate-500 hover:underline">&larr; Portfólios</a>
                <h1 class="text-2xl font-semibold text-slate-800">{{ $portfolio->name }}</h1>
            </div>
            <div class="flex items-center gap-2">
                <button wire:click="queueRefreshAll" wire:confirm="Enfileirar atualização de TODAS as empresas deste portfólio?"
                        class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                    Atualizar todas
                </button>
            </div>
        </div>

        @if (session('status'))
            <div class="rounded-md bg-emerald-50 px-4 py-3 text-sm text-emerald-800 ring-1 ring-emerald-600/20">
                {{ session('status') }}
            </div>
        @endif

        {{-- Live progress: polls every 6s while the queue drains --}}
        <div wire:poll.6s class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-slate-200">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-700">Progresso do monitoramento</h2>
                <span class="text-xs text-slate-400">atualiza automaticamente · {{ $stats['queued'] }} job(s) na fila global</span>
            </div>
            @php
                $done = $stats['ok'] + $stats['not_found'] + $stats['errors'];
                $pct = $stats['total'] > 0 ? round($done / $stats['total'] * 100) : 0;
            @endphp
            <div class="mb-3 h-2 w-full overflow-hidden rounded-full bg-slate-100">
                <div class="h-2 rounded-full bg-indigo-500 transition-all" style="width: {{ $pct }}%"></div>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-5">
                <div><p class="text-xs uppercase text-slate-400">Total</p><p class="text-xl font-semibold text-slate-800">{{ $stats['total'] }}</p></div>
                <div><p class="text-xs uppercase text-slate-400">Atualizadas</p><p class="text-xl font-semibold text-emerald-700">{{ $stats['ok'] }}</p></div>
                <div><p class="text-xs uppercase text-slate-400">Não encontradas</p><p class="text-xl font-semibold text-slate-600">{{ $stats['not_found'] }}</p></div>
                <div><p class="text-xs uppercase text-slate-400">Erros</p><p class="text-xl font-semibold text-red-700">{{ $stats['errors'] }}</p></div>
                <div><p class="text-xs uppercase text-slate-400">Pendentes</p><p class="text-xl font-semibold text-amber-600">{{ $stats['pending'] }}</p></div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Add a single company --}}
            <form wire:submit="addCompany" class="space-y-3 rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <h2 class="text-base font-semibold text-slate-800">Adicionar empresa</h2>
                <div>
                    <label for="cnpj" class="block text-sm font-medium text-slate-700">CNPJ</label>
                    <input wire:model="cnpj" id="cnpj" type="text" placeholder="00.000.000/0001-91"
                           class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('cnpj') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="label" class="block text-sm font-medium text-slate-700">Rótulo (opcional)</label>
                    <input wire:model="label" id="label" type="text" placeholder="Ex.: Fornecedor principal"
                           class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <button type="submit" class="rounded-md bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">Adicionar</button>
            </form>

            {{-- CSV import --}}
            <form wire:submit="import" class="space-y-3 rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <h2 class="text-base font-semibold text-slate-800">Importar CSV</h2>
                <p class="text-sm text-slate-500">Colunas: <code class="rounded bg-slate-100 px-1">cnpj</code>, <code class="rounded bg-slate-100 px-1">label</code> (opcional).</p>
                <input wire:model="csv" type="file" accept=".csv,.txt"
                       class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-md file:border-0 file:bg-slate-100 file:px-4 file:py-2 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200">
                @error('csv') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                <div wire:loading wire:target="csv" class="text-sm text-slate-500">Enviando arquivo…</div>
                <button type="submit" class="rounded-md bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">Importar</button>

                @if ($importReport !== null)
                    <div class="mt-2 rounded-md bg-slate-50 p-3 text-sm ring-1 ring-slate-200">
                        <p class="font-medium text-slate-700">{{ $importReport['imported'] }} adicionada(s), {{ count($importReport['rejected']) }} rejeitada(s).</p>
                        @if (count($importReport['rejected']) > 0)
                            <ul class="mt-2 max-h-40 space-y-1 overflow-y-auto text-slate-500">
                                @foreach ($importReport['rejected'] as $row)
                                    <li>Linha {{ $row['line'] }}: <span class="font-mono">{{ $row['value'] ?: '(vazio)' }}</span> — {{ $row['reason'] }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif
            </form>
        </div>

        {{-- Filtro por status da coleta: filtra a lista e permite revalidar só o subconjunto --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap gap-2">
                @foreach ($filters as $value => $f)
                    <button wire:click="setStatusFilter('{{ $value }}')"
                            @class([
                                'inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-medium ring-1 ring-inset',
                                'bg-indigo-600 text-white ring-indigo-600' => $statusFilter === $value,
                                'bg-white text-slate-600 ring-slate-200 hover:bg-slate-50' => $statusFilter !== $value,
                            ])>
                        {{ $f['label'] }}
                        <span @class([
                            'rounded-full px-1.5 text-xs',
                            'bg-indigo-500 text-white' => $statusFilter === $value,
                            'bg-slate-100 text-slate-600' => $statusFilter !== $value,
                        ])>{{ $f['count'] }}</span>
                    </button>
                @endforeach
            </div>
            @if ($statusFilter !== 'all')
                <button wire:click="reprocessFiltered"
                        wire:confirm="Revalidar as {{ $filters[$statusFilter]['count'] }} empresa(s) do filtro '{{ $filters[$statusFilter]['label'] }}'?"
                        class="inline-flex items-center rounded-md bg-slate-800 px-3 py-2 text-sm font-medium text-white hover:bg-slate-700">
                    Revalidar filtrados ({{ $filters[$statusFilter]['count'] }})
                </button>
            @endif
        </div>

        {{-- Companies table --}}
        <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th class="px-5 py-3">Empresa</th>
                        <th class="px-5 py-3">Situação</th>
                        <th class="px-5 py-3">Status da coleta</th>
                        <th class="px-5 py-3">Atualizado</th>
                        <th class="px-5 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($companies as $company)
                        <tr>
                            <td class="px-5 py-3">
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                    <a href="{{ route('companies.show', $company) }}" wire:navigate class="font-medium text-indigo-700 hover:underline">
                                        {{ $company->latestSnapshot?->razao_social ?? $company->label ?? 'Sem dados ainda' }}
                                    </a>
                                    @if ($company->label !== null && $company->latestSnapshot?->razao_social !== null)
                                        <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-200" title="Seu rótulo para esta unidade">
                                            <svg class="h-3 w-3 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                                            </svg>
                                            {{ $company->label }}
                                        </span>
                                    @endif
                                </div>
                                <p class="font-mono text-xs text-slate-500">{{ $company->formattedCnpj() }}</p>
                            </td>
                            <td class="px-5 py-3 text-sm text-slate-600">{{ $company->latestSnapshot?->situacao_cadastral ?? '—' }}</td>
                            <td class="px-5 py-3 text-sm">
                                @if ($company->last_refresh_status === null)
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">Pendente</span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $company->last_refresh_status->badgeClasses() }}">
                                        {{ $company->last_refresh_status->label() }}
                                    </span>
                                    @if ($company->last_refresh_error)
                                        <p class="mt-1 max-w-xs truncate text-xs text-red-500" title="{{ $company->last_refresh_error }}">{{ $company->last_refresh_error }}</p>
                                    @endif
                                @endif
                            </td>
                            <td class="px-5 py-3 text-sm text-slate-500">{{ $company->last_refreshed_at?->diffForHumans() ?? 'Nunca' }}</td>
                            <td class="px-5 py-3 text-right text-sm">
                                <button wire:click="queueRefresh({{ $company->id }})" class="rounded-md px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50">Atualizar</button>
                                <button wire:click="removeCompany({{ $company->id }})" wire:confirm="Remover esta empresa do monitoramento?" class="rounded-md px-2 py-1 font-medium text-red-600 hover:bg-red-50">Remover</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-sm text-slate-500">
                                {{ $statusFilter === 'all' ? 'Nenhuma empresa neste portfólio ainda.' : 'Nenhuma empresa neste filtro.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $companies->links() }}</div>

        {{-- Agendamento automático --}}
        @php
            $todayDom = now()->day;
            $sel = $scheduleDays; // normalizado/ordenado no componente
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

        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">Agendamento automático</h2>
                        <p class="text-sm text-slate-500">
                            @if (count($sel) > 0)
                                Revalida os CNPJs sozinho, todo mês {{ $dayPhrase($sel) }}.
                            @else
                                Defina quando este portfólio deve revalidar os CNPJs sozinho.
                            @endif
                        </p>
                    </div>
                </div>
                @if (count($sel) > 0)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Ativo
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-500 ring-1 ring-inset ring-slate-500/15">
                        <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> Inativo
                    </span>
                @endif
            </div>

            <div class="grid grid-cols-1 gap-6 p-5 lg:grid-cols-[minmax(0,1fr)_20rem]">
                {{-- Calendário de dias do mês --}}
                <div>
                    <div class="mb-3 flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Dias do mês</p>
                        @if (count($sel) > 0)
                            <button type="button" wire:click="clearScheduleDays"
                                    class="inline-flex cursor-pointer items-center gap-1 text-xs font-medium text-slate-400 transition-colors hover:text-red-600">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                                Limpar
                            </button>
                        @endif
                    </div>
                    <div class="grid grid-cols-7 gap-1.5" role="group" aria-label="Dias do mês para agendamento">
                        @for ($d = 1; $d <= 31; $d++)
                            @php $on = in_array($d, $sel, true); @endphp
                            <button type="button" wire:click="toggleScheduleDay({{ $d }})"
                                    aria-pressed="{{ $on ? 'true' : 'false' }}"
                                    aria-label="Dia {{ $d }}{{ $d === $todayDom ? ' — hoje' : '' }}"
                                    @class([
                                        'relative flex h-10 items-center justify-center rounded-lg text-sm font-medium cursor-pointer transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1',
                                        'bg-indigo-600 text-white ring-1 ring-inset ring-indigo-600 hover:bg-indigo-500' => $on,
                                        'bg-white text-slate-600 ring-1 ring-inset ring-slate-200 hover:bg-slate-50 hover:text-slate-900' => ! $on,
                                        'ring-2 ring-indigo-400' => $d === $todayDom && ! $on,
                                    ])>
                                {{ $d }}
                                @if ($d === $todayDom)
                                    <span class="absolute bottom-1 h-1 w-1 rounded-full {{ $on ? 'bg-white/80' : 'bg-indigo-500' }}"></span>
                                @endif
                            </button>
                        @endfor
                    </div>
                    <p class="mt-3 flex items-start gap-2 text-xs text-slate-400">
                        <span class="mt-1 inline-block h-1.5 w-1.5 shrink-0 rounded-full bg-indigo-500"></span>
                        Hoje é dia {{ $todayDom }}. Dias 29–31 rodam no último dia do mês quando ele não existe (ex.: 31 cai em 28/fev).
                    </p>
                </div>

                {{-- Atalhos + resumo + próxima execução + salvar --}}
                <div class="flex flex-col gap-4 lg:border-l lg:border-slate-100 lg:pl-6">
                    <div>
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Atalhos</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($presets as $preset)
                                @php $active = $sel === $preset['days']; @endphp
                                <button type="button" wire:click="applyPreset('{{ $preset['key'] }}')"
                                        @class([
                                            'rounded-full px-3 py-1.5 text-sm font-medium cursor-pointer transition-colors duration-150 ring-1 ring-inset focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500',
                                            'bg-indigo-600 text-white ring-indigo-600' => $active,
                                            'bg-white text-slate-600 ring-slate-200 hover:bg-slate-50 hover:text-slate-900' => ! $active,
                                        ])>
                                    {{ $preset['label'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    @if ($nextRun !== null)
                        @php $diasAte = (int) now()->startOfDay()->diffInDays($nextRun); @endphp
                        <div class="rounded-lg bg-indigo-50/70 p-3 ring-1 ring-inset ring-indigo-100">
                            <div class="flex items-center gap-1.5 text-indigo-700">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <span class="text-xs font-semibold uppercase tracking-wide">Próxima execução</span>
                            </div>
                            <p class="mt-1 text-sm font-semibold text-slate-800">{{ $nextRun->format('d/m/Y') }}</p>
                            <p class="text-xs text-slate-500">
                                @if ($diasAte === 0)
                                    hoje, às 3h da manhã
                                @elseif ($diasAte === 1)
                                    amanhã, às 3h da manhã
                                @else
                                    em {{ $diasAte }} dias
                                @endif
                            </p>
                        </div>
                    @else
                        <div class="rounded-lg bg-slate-50 p-3 ring-1 ring-inset ring-slate-100">
                            <p class="text-sm text-slate-500">
                                Sem execução agendada. As coletas só rodam quando você clica em
                                <span class="font-medium text-slate-700">Atualizar</span>.
                            </p>
                        </div>
                    @endif

                    <div class="mt-auto">
                        @if ($scheduleDirty)
                            <div class="flex flex-col gap-2">
                                <p class="flex items-center gap-1.5 text-xs font-medium text-amber-600">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Alterações não salvas
                                </p>
                                <div class="flex items-center gap-2">
                                    <button wire:click="saveSchedule" wire:loading.attr="disabled" wire:target="saveSchedule"
                                            class="inline-flex flex-1 cursor-pointer items-center justify-center gap-1.5 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-slate-700 disabled:opacity-60">
                                        <span wire:loading.remove wire:target="saveSchedule">Salvar agendamento</span>
                                        <span wire:loading wire:target="saveSchedule">Salvando…</span>
                                    </button>
                                    <button wire:click="discardScheduleChanges"
                                            class="cursor-pointer rounded-lg px-3 py-2 text-sm font-medium text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-700">
                                        Descartar
                                    </button>
                                </div>
                            </div>
                        @else
                            <p class="flex items-center gap-1.5 text-sm {{ count($sel) > 0 ? 'text-emerald-600' : 'text-slate-400' }}">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                {{ count($sel) > 0 ? 'Agendamento salvo' : 'Nenhum agendamento' }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Histórico de execuções --}}
        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <h2 class="text-base font-semibold text-slate-800">Histórico de execuções</h2>
                @if ($scheduledRuns->isNotEmpty())
                    <span class="text-xs text-slate-400">últimas {{ $scheduledRuns->count() }}</span>
                @endif
            </div>
            <ul role="list" class="divide-y divide-slate-100">
                @forelse ($scheduledRuns as $run)
                    <li class="flex items-center justify-between px-5 py-3 text-sm">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                            </span>
                            <div>
                                <span class="font-medium text-slate-700">{{ $run->ran_on->format('d/m/Y') }}</span>
                                <span class="text-slate-400">· agendado p/ dia {{ $run->scheduled_day }}</span>
                            </div>
                        </div>
                        <span class="text-slate-500">{{ $run->companies_count }} empresa(s)</span>
                    </li>
                @empty
                    <li class="px-5 py-10 text-center text-sm text-slate-500">
                        Nenhuma execução automática ainda. Quando um dia agendado chegar, ela aparece aqui.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
