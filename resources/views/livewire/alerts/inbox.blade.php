<div class="py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">Alertas</h1>
                <p class="text-sm text-slate-500">Mudanças detectadas que ainda não foram revisadas.</p>
            </div>
            <button wire:click="acknowledgeAll" wire:confirm="Marcar como vistos todos os alertas do filtro atual?"
                    class="rounded-md px-3 py-2 text-sm font-medium text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50">
                Marcar todos como vistos
            </button>
        </div>

        @if (session('status'))
            <div class="rounded-md bg-emerald-50 px-4 py-3 text-sm text-emerald-800 ring-1 ring-emerald-600/20">
                {{ session('status') }}
            </div>
        @endif

        {{-- Severity filter --}}
        <div class="flex flex-wrap gap-2">
            @foreach (['all' => 'Todos', 'critical' => 'Críticos', 'high' => 'Altos', 'medium' => 'Médios', 'low' => 'Baixos'] as $value => $label)
                <button wire:click="setSeverity('{{ $value }}')"
                        @class([
                            'rounded-full px-3 py-1 text-sm font-medium ring-1 ring-inset',
                            'bg-indigo-600 text-white ring-indigo-600' => $severity === $value,
                            'bg-white text-slate-600 ring-slate-200 hover:bg-slate-50' => $severity !== $value,
                        ])>
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
            <ul role="list" class="divide-y divide-slate-100">
                @forelse ($events as $event)
                    <li class="flex items-start justify-between gap-4 px-5 py-4">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset {{ $event->severity->badgeClasses() }}">{{ $event->severity->label() }}</span>
                                <a href="{{ route('companies.show', $event->monitored_company_id) }}" wire:navigate class="truncate text-sm font-medium text-indigo-700 hover:underline">
                                    {{ $event->monitoredCompany->label ?? $event->monitoredCompany->formattedCnpj() }}
                                </a>
                            </div>
                            <p class="mt-1 text-sm text-slate-600">{{ $event->type->label() }}</p>
                            <p class="text-sm text-slate-500">
                                <span class="text-slate-400">{{ $event->old_value ?? '—' }}</span> &rarr; <span class="text-slate-600">{{ $event->new_value ?? '—' }}</span>
                            </p>
                            <p class="mt-1 text-xs text-slate-400">{{ $event->detected_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <button wire:click="acknowledge({{ $event->id }})"
                                class="shrink-0 rounded-md bg-slate-800 px-3 py-1.5 text-sm font-medium text-white hover:bg-slate-700">
                            Marcar como visto
                        </button>
                    </li>
                @empty
                    <li class="px-5 py-10 text-center text-sm text-slate-500">Nenhum alerta em aberto. 🎉</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
