<div class="mx-auto max-w-[1100px] px-5 py-8 sm:px-8">
    <a href="{{ route('admin.organizations.index') }}" wire:navigate class="mb-4 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink-2">
        <x-ui.icon name="arrow_back" :size="18" />Organizações
    </a>

    @if (session('status'))
        <div class="mb-5 rounded-card border border-ok bg-ok-soft px-4 py-3 text-sm font-medium text-ok">
            {{ session('status') }}
        </div>
    @endif

    {{-- Cabeçalho --}}
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl font-bold tracking-tight text-ink">{{ $organization->name }}</h1>
                @if ($organization->isSuspended())
                    <x-ui.badge tone="crit" dot>Suspensa</x-ui.badge>
                @else
                    <x-ui.badge tone="ok" dot>Ativa</x-ui.badge>
                @endif
            </div>
            <p class="mt-1 text-sm text-ink-muted">
                Criada {{ $organization->created_at?->diffForHumans() }}
                @if ($organization->isSuspended()) · suspensa {{ $organization->suspended_at?->diffForHumans() }} @endif
            </p>
        </div>

        @if ($organization->isSuspended())
            <x-ui.button variant="secondary" icon="play_circle" wire:click="reactivate">Reativar</x-ui.button>
        @else
            <x-ui.button variant="danger" icon="block" wire:click="suspend"
                         wire:confirm="Suspender esta organização? Os usuários dela não conseguirão entrar.">Suspender</x-ui.button>
        @endif
    </div>

    {{-- Uso --}}
    <div class="mb-6 grid grid-cols-3 gap-3">
        <div class="rounded-card border border-line bg-surface px-4 py-3.5 shadow-card">
            <div class="text-[11px] font-semibold uppercase tracking-wide text-ink-muted">Usuários</div>
            <div class="mt-1 text-2xl font-bold text-ink">{{ $users->count() }}</div>
        </div>
        <div class="rounded-card border border-line bg-surface px-4 py-3.5 shadow-card">
            <div class="text-[11px] font-semibold uppercase tracking-wide text-ink-muted">Portfólios</div>
            <div class="mt-1 text-2xl font-bold text-ink">{{ $portfolios->count() }}</div>
        </div>
        <div class="rounded-card border border-line bg-surface px-4 py-3.5 shadow-card">
            <div class="text-[11px] font-semibold uppercase tracking-wide text-ink-muted">Empresas</div>
            <div class="mt-1 text-2xl font-bold {{ $companiesTotal > $maxCompanies ? 'text-crit' : 'text-ink' }}">
                {{ $companiesTotal }} <span class="text-base font-medium text-ink-muted">/ {{ $maxCompanies }}</span>
            </div>
        </div>
    </div>

    {{-- Plano / quota --}}
    <div class="mb-6 rounded-card border border-line bg-surface p-5 shadow-card">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-[15px] font-semibold text-ink">Plano</h2>
                <p class="mt-0.5 text-[12.5px] text-ink-muted">Define a quota de CNPJs monitorados da organização.</p>
            </div>
            <div class="flex flex-wrap gap-1.5">
                @foreach ($plans as $plan)
                    @php $active = $organization->plan === $plan; @endphp
                    <button type="button" wire:click="setPlan('{{ $plan->value }}')"
                            @class([
                                'rounded-btn px-3.5 py-2 text-sm font-semibold transition-colors border cursor-pointer',
                                'bg-primary text-onprimary border-primary' => $active,
                                'bg-surface text-ink-2 border-line-strong hover:bg-surface-2' => ! $active,
                            ])>
                        {{ $plan->label() }}
                        <span class="ml-1 text-[11px] font-normal opacity-80">{{ $plan->maxCompanies() }} CNPJs</span>
                    </button>
                @endforeach
            </div>
        </div>
        @if ($companiesTotal > $maxCompanies)
            <p class="mt-3 rounded-btn border border-crit bg-crit-soft px-3 py-2 text-[12.5px] text-crit">
                Esta organização já monitora mais CNPJs do que o plano atual permite ({{ $companiesTotal }} de {{ $maxCompanies }}). Ela não conseguirá adicionar novos até fazer upgrade.
            </p>
        @endif
    </div>

    <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
        {{-- Usuários --}}
        <div class="overflow-hidden rounded-card border border-line bg-surface shadow-card">
            <div class="border-b border-line px-5 py-3.5">
                <h2 class="text-[15px] font-semibold text-ink">Usuários</h2>
                @error('role') <span class="mt-1 block text-xs text-crit">{{ $message }}</span> @enderror
            </div>
            <table class="w-full text-left">
                <tbody>
                    @foreach ($users as $u)
                        <tr class="border-b border-line last:border-0">
                            <td class="px-5 py-3.5">
                                <div class="font-medium text-ink">{{ $u->name }}</div>
                                <div class="text-[12.5px] text-ink-muted">{{ $u->email }}</div>
                            </td>
                            <td class="px-3 py-3.5">
                                @if ($u->isAdmin())
                                    <x-ui.badge tone="high">Administrador</x-ui.badge>
                                @else
                                    <x-ui.badge tone="muted">Usuário</x-ui.badge>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1.5">
                                    <x-ui.button variant="ghost" size="sm" icon="visibility" wire:click="impersonate({{ $u->id }})"
                                                 wire:confirm="Entrar como {{ $u->name }}? Você poderá voltar ao admin pelo banner.">Personificar</x-ui.button>
                                    @if ($u->isAdmin())
                                        <x-ui.button variant="secondary" size="sm" wire:click="setRole({{ $u->id }}, 'user')">Rebaixar</x-ui.button>
                                    @else
                                        <x-ui.button variant="secondary" size="sm" wire:click="setRole({{ $u->id }}, 'admin')">Promover a admin</x-ui.button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Portfólios --}}
        <div class="h-fit overflow-hidden rounded-card border border-line bg-surface shadow-card">
            <div class="border-b border-line px-5 py-3.5">
                <h2 class="text-[15px] font-semibold text-ink">Portfólios</h2>
            </div>
            <ul>
                @forelse ($portfolios as $p)
                    <li class="flex items-center justify-between border-b border-line px-5 py-3 last:border-0">
                        <span class="truncate text-sm text-ink-2">{{ $p->name }}</span>
                        <span class="ml-3 shrink-0 font-mono text-[13px] text-ink-muted">{{ $p->monitored_companies_count }} empresa(s)</span>
                    </li>
                @empty
                    <li class="px-5 py-6 text-center text-sm text-ink-muted">Nenhum portfólio.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
