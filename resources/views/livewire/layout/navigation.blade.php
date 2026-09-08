<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

@php
    $navItems = [
        ['route' => 'dashboard', 'active' => 'dashboard', 'icon' => 'dashboard', 'label' => 'Painel'],
        ['route' => 'portfolios.index', 'active' => 'portfolios.*', 'icon' => 'folder_open', 'label' => 'Portfólios'],
        ['route' => 'alerts.index', 'active' => 'alerts.*', 'icon' => 'notifications', 'label' => 'Alertas'],
    ];

    if (auth()->user()?->isAdmin()) {
        $navItems[] = ['route' => 'users.index', 'active' => 'users.*', 'icon' => 'group', 'label' => 'Usuários'];
    }
@endphp

<header x-data="{
            open: false,
            theme: (function () { try { return localStorage.getItem('vigilo-theme') === 'dark' ? 'dark' : 'light'; } catch (e) { return 'light'; } })(),
            toggle() {
                this.theme = this.theme === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-theme', this.theme);
                try { localStorage.setItem('vigilo-theme', this.theme); } catch (e) {}
            }
        }"
        class="sticky top-0 z-20 border-b border-line bg-surface shadow-card">
    <div class="mx-auto flex h-[62px] max-w-[1200px] items-center gap-6 px-5 sm:px-8">
        {{-- Marca --}}
        <a href="{{ route('dashboard') }}" wire:navigate class="flex shrink-0 items-center gap-2.5">
            <span class="flex h-[33px] w-[33px] items-center justify-center rounded-[9px] text-[17px] font-bold text-white shadow-card"
                  style="background:linear-gradient(135deg,var(--logo-a),var(--logo-b));">V</span>
            <span class="text-[17px] font-semibold tracking-tight text-ink">Vigilo</span>
        </a>

        {{-- Navegação (desktop) --}}
        <nav class="hidden items-center gap-1 sm:flex">
            @foreach ($navItems as $item)
                @php $on = request()->routeIs($item['active']); @endphp
                <a href="{{ route($item['route']) }}" wire:navigate
                   @class([
                       'flex items-center gap-1.5 rounded-lg px-3.5 py-2 text-sm font-medium transition-colors',
                       'bg-primary-soft text-primary' => $on,
                       'text-ink-2 hover:bg-surface-2' => ! $on,
                   ])>
                    <x-ui.icon :name="$item['icon']" :size="19" />{{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- Ações à direita --}}
        <div class="ml-auto flex items-center gap-2.5">
            <button @click="toggle()" title="Alternar tema"
                    class="flex h-9 w-9 items-center justify-center rounded-[9px] border border-line bg-surface-2 text-ink-2 transition-colors hover:bg-surface-3">
                <span class="msym" style="font-size:20px;" x-text="theme === 'dark' ? 'light_mode' : 'dark_mode'" aria-hidden="true">dark_mode</span>
            </button>

            <div class="relative" x-data="{ menu: false }" @click.outside="menu = false">
                <button @click="menu = ! menu"
                        class="flex items-center gap-2 rounded-full border border-line bg-surface-2 py-1 pl-1 pr-2.5 transition-colors hover:bg-surface-3">
                    <span class="flex h-[26px] w-[26px] items-center justify-center rounded-full bg-surface-3">
                        <x-ui.icon name="person" :size="18" class="text-ink-muted" />
                    </span>
                    <span class="hidden max-w-[120px] truncate text-[13.5px] font-medium text-ink-2 sm:block">{{ auth()->user()->name }}</span>
                    <x-ui.icon name="expand_more" :size="18" class="text-ink-muted" />
                </button>
                <div x-show="menu" x-transition x-cloak
                     class="absolute right-0 z-30 mt-2 w-48 overflow-hidden rounded-xl border border-line bg-surface py-1 shadow-float">
                    <a href="{{ route('profile') }}" wire:navigate class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-ink-2 transition-colors hover:bg-surface-2">
                        <x-ui.icon name="settings" :size="18" />Perfil
                    </a>
                    <button wire:click="logout" class="flex w-full items-center gap-2.5 px-4 py-2.5 text-sm text-ink-2 transition-colors hover:bg-surface-2">
                        <x-ui.icon name="logout" :size="18" />Sair
                    </button>
                </div>
            </div>

            <button @click="open = ! open"
                    class="flex h-9 w-9 items-center justify-center rounded-[9px] border border-line bg-surface-2 text-ink-2 sm:hidden">
                <span class="msym" style="font-size:20px;" x-text="open ? 'close' : 'menu'" aria-hidden="true">menu</span>
            </button>
        </div>
    </div>

    {{-- Navegação (mobile) --}}
    <div x-show="open" x-cloak class="space-y-1 border-t border-line bg-surface px-4 py-3 sm:hidden">
        @foreach ($navItems as $item)
            @php $on = request()->routeIs($item['active']); @endphp
            <a href="{{ route($item['route']) }}" wire:navigate
               @class([
                   'flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm font-medium',
                   'bg-primary-soft text-primary' => $on,
                   'text-ink-2 hover:bg-surface-2' => ! $on,
               ])>
                <x-ui.icon :name="$item['icon']" :size="20" />{{ $item['label'] }}
            </a>
        @endforeach
    </div>
</header>
