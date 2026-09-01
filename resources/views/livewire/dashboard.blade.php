<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Painel</h1>
                <p class="text-sm text-slate-500">Due diligence contínua da sua carteira de CNPJs.</p>
            </div>
            <a href="{{ route('alerts.index') }}" wire:navigate
               class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                Ver alertas
            </a>
        </div>

        @if (session('status'))
            <div class="rounded-md bg-emerald-50 px-4 py-3 text-sm text-emerald-800 ring-1 ring-emerald-600/20">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm font-medium text-slate-500">Empresas monitoradas</p>
                <p class="mt-2 text-3xl font-semibold text-slate-800">{{ $monitoredCount }}</p>
            </div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm font-medium text-slate-500">Portfólios</p>
                <p class="mt-2 text-3xl font-semibold text-slate-800">{{ $portfoliosCount }}</p>
            </div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm font-medium text-slate-500">Alertas abertos</p>
                <p class="mt-2 text-3xl font-semibold text-slate-800">{{ $openAlertsCount }}</p>
            </div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-red-200">
                <p class="text-sm font-medium text-red-600">Alertas críticos abertos</p>
                <p class="mt-2 text-3xl font-semibold text-red-700">{{ $openCriticalCount }}</p>
            </div>
        </div>

        <div class="rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="text-base font-semibold text-slate-800">Últimas mudanças</h2>
            </div>
            <ul role="list" class="divide-y divide-slate-100">
                @forelse ($recentChanges as $change)
                    <li class="flex items-center justify-between gap-4 px-5 py-3">
                        <div class="min-w-0">
                            <a href="{{ route('companies.show', $change->monitored_company_id) }}" wire:navigate
                               class="truncate text-sm font-medium text-indigo-700 hover:underline">
                                {{ $change->monitoredCompany->label ?? $change->monitoredCompany->formattedCnpj() }}
                            </a>
                            <p class="truncate text-sm text-slate-500">
                                {{ $change->type->label() }}
                                @if ($change->old_value !== null || $change->new_value !== null)
                                    — <span class="text-slate-400">{{ $change->old_value ?? '—' }}</span>
                                    &rarr; <span class="text-slate-600">{{ $change->new_value ?? '—' }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="flex shrink-0 items-center gap-3">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $change->severity->badgeClasses() }}">
                                {{ $change->severity->label() }}
                            </span>
                            <span class="text-xs text-slate-400">{{ $change->detected_at->diffForHumans() }}</span>
                        </div>
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-sm text-slate-500">
                        Nenhuma mudança detectada ainda. Adicione empresas a um portfólio e clique em “Atualizar”.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
