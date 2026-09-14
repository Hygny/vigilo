<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/login', navigate: true);
    }
}; ?>

<header x-data="{
            theme: (function () { try { return localStorage.getItem('vigilo-theme') === 'dark' ? 'dark' : 'light'; } catch (e) { return 'light'; } })(),
            toggle() {
                this.theme = this.theme === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-theme', this.theme);
                try { localStorage.setItem('vigilo-theme', this.theme); } catch (e) {}
            }
        }"
        class="sticky top-0 z-20 border-b border-line bg-surface shadow-card">
    <div class="mx-auto flex h-[62px] max-w-[1200px] items-center gap-6 px-5 sm:px-8">
        <a href="{{ route('admin.organizations.index') }}" wire:navigate class="flex shrink-0 items-center gap-2.5">
            <span class="flex h-[33px] w-[33px] items-center justify-center rounded-[9px] text-[17px] font-bold text-white shadow-card"
                  style="background:linear-gradient(135deg,var(--logo-a),var(--logo-b));">V</span>
            <span class="text-[17px] font-semibold tracking-tight text-ink">Vigilo</span>
            <span class="rounded-md bg-primary-soft px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wide text-primary">Admin</span>
        </a>

        <nav class="hidden items-center gap-1 sm:flex">
            @php $on = request()->routeIs('admin.organizations.*'); @endphp
            <a href="{{ route('admin.organizations.index') }}" wire:navigate
               @class([
                   'flex items-center gap-1.5 rounded-lg px-3.5 py-2 text-sm font-medium transition-colors',
                   'bg-primary-soft text-primary' => $on,
                   'text-ink-2 hover:bg-surface-2' => ! $on,
               ])>
                <x-ui.icon name="apartment" :size="19" />Organizações
            </a>
        </nav>

        <div class="ml-auto flex items-center gap-2.5">
            <button @click="toggle()" title="Alternar tema"
                    class="flex h-9 w-9 items-center justify-center rounded-[9px] border border-line bg-surface-2 text-ink-2 transition-colors hover:bg-surface-3">
                <span class="msym" style="font-size:20px;" x-text="theme === 'dark' ? 'light_mode' : 'dark_mode'" aria-hidden="true">dark_mode</span>
            </button>

            <span class="hidden max-w-[160px] truncate text-[13.5px] font-medium text-ink-2 sm:block">{{ auth()->user()->name }}</span>

            <button wire:click="logout"
                    class="flex items-center gap-2 rounded-[9px] border border-line bg-surface-2 px-3 py-2 text-sm text-ink-2 transition-colors hover:bg-surface-3">
                <x-ui.icon name="logout" :size="18" />Sair
            </button>
        </div>
    </div>
</header>
