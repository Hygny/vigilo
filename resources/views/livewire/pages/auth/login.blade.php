<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <h2 class="mb-1 text-xl font-bold tracking-tight text-ink">Entrar</h2>
    <p class="mb-6 text-sm text-ink-muted">Acesse o painel do Vigilo.</p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-4">
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="form.email" id="email" class="mt-1.5 block w-full" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Senha')" />
            <x-text-input wire:model="form.password" id="password" class="mt-1.5 block w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <label for="remember" class="inline-flex items-center gap-2">
            <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-line-strong bg-surface-2 text-primary shadow-sm focus:ring-focus" name="remember">
            <span class="text-sm text-ink-2">{{ __('Lembrar de mim') }}</span>
        </label>

        <div class="flex items-center justify-end gap-4 pt-1">
            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-ink-muted underline-offset-2 hover:text-ink-2 hover:underline" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Esqueceu a senha?') }}
                </a>
            @endif
            <x-primary-button>{{ __('Entrar') }}</x-primary-button>
        </div>
    </form>
</div>
