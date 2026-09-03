@php
    $me = auth()->id();
    $roleTones = ['admin' => 'high', 'user' => 'muted'];
    $roleIcons = ['admin' => 'admin_panel_settings', 'user' => 'person'];
@endphp

<div class="py-8">
    <div class="mx-auto max-w-[1200px] space-y-6 px-5 sm:px-8">
        <div>
            <h1 class="text-[28px] font-bold tracking-[-0.03em] text-ink">Usuários</h1>
            <p class="mt-1 text-[14.5px] text-ink-muted">Gerencie quem acessa esta organização e os papéis de cada um.</p>
        </div>

        @if (session('status'))
            <div class="flex items-center gap-2 rounded-btn border border-line bg-surface px-4 py-3 text-sm text-ink-2 shadow-card">
                <x-ui.icon name="check_circle" :size="18" class="text-ok" />{{ session('status') }}
            </div>
        @endif
        @error('delete') <div class="rounded-btn border border-crit bg-surface px-4 py-3 text-sm text-crit">{{ $message }}</div> @enderror
        @error('role') <div class="rounded-btn border border-crit bg-surface px-4 py-3 text-sm text-crit">{{ $message }}</div> @enderror

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            {{-- Criar usuário --}}
            <form wire:submit="createUser" class="space-y-3.5 rounded-card border border-line bg-surface p-[22px] shadow-card">
                <div class="flex items-center gap-2"><x-ui.icon name="person_add" :size="20" class="text-accent" /><h2 class="text-[15px] font-semibold text-ink">Adicionar usuário</h2></div>
                <div>
                    <label for="u-name" class="mb-1.5 block text-[13px] font-semibold text-ink">Nome</label>
                    <div class="ui-input-wrap"><x-ui.icon name="badge" :size="19" class="text-ink-muted" /><input wire:model="name" id="u-name" type="text" placeholder="Ex.: Maria Silva" class="ui-input" /></div>
                    @error('name') <p class="mt-1 text-sm text-crit">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="u-email" class="mb-1.5 block text-[13px] font-semibold text-ink">E-mail</label>
                    <div class="ui-input-wrap"><x-ui.icon name="mail" :size="19" class="text-ink-muted" /><input wire:model="email" id="u-email" type="email" placeholder="maria@empresa.com" class="ui-input" /></div>
                    @error('email') <p class="mt-1 text-sm text-crit">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="u-password" class="mb-1.5 block text-[13px] font-semibold text-ink">Senha</label>
                    <div class="ui-input-wrap"><x-ui.icon name="lock" :size="19" class="text-ink-muted" /><input wire:model="password" id="u-password" type="text" placeholder="mínimo 8 caracteres" class="ui-input" /></div>
                    @error('password') <p class="mt-1 text-sm text-crit">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-[13px] font-semibold text-ink">Papel</label>
                    <div class="flex gap-2">
                        @foreach (['user' => 'Usuário', 'admin' => 'Administrador'] as $val => $lbl)
                            <button type="button" wire:click="$set('role', '{{ $val }}')"
                                    @class([
                                        'flex-1 rounded-btn border px-3 py-2 text-[13px] font-semibold cursor-pointer transition-colors',
                                        'bg-primary text-onprimary border-primary' => $role === $val,
                                        'bg-surface text-ink-2 border-line-strong hover:bg-surface-2' => $role !== $val,
                                    ])>{{ $lbl }}</button>
                        @endforeach
                    </div>
                </div>
                <x-ui.button type="submit" icon="add">Criar usuário</x-ui.button>
            </form>

            {{-- Permissões --}}
            <div class="rounded-card border border-line bg-surface p-[22px] shadow-card">
                <div class="mb-4 flex items-center gap-2"><x-ui.icon name="shield_person" :size="20" class="text-accent" /><h2 class="text-[15px] font-semibold text-ink">Permissões</h2></div>
                <div class="space-y-3">
                    <div class="rounded-btn border border-line bg-surface-2 p-4">
                        <div class="mb-1.5"><x-ui.badge tone="high" icon="admin_panel_settings">Administrador</x-ui.badge></div>
                        <p class="text-[13.5px] text-ink-2">Gerencia os usuários e papéis desta organização (criar, excluir, promover) e usa todo o app — carteiras, importação, agendamentos e alertas.</p>
                    </div>
                    <div class="rounded-btn border border-line bg-surface-2 p-4">
                        <div class="mb-1.5"><x-ui.badge tone="muted" icon="person">Usuário</x-ui.badge></div>
                        <p class="text-[13.5px] text-ink-2">Usa o app — vê as carteiras, empresas e alertas. Não acessa a gestão de usuários.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lista de usuários --}}
        <x-ui.card>
            <div class="flex items-center justify-between border-b border-line px-[22px] py-4">
                <h2 class="text-[15px] font-semibold text-ink">Usuários da organização</h2>
                <span class="font-mono text-xs text-ink-muted">{{ $users->count() }} no total</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left">
                    <thead>
                        <tr class="border-b border-line bg-surface-2 text-[11.5px] font-semibold uppercase tracking-wide text-ink-muted">
                            <th class="px-[22px] py-3.5">Usuário</th>
                            <th class="px-4 py-3.5">Papel</th>
                            <th class="px-[22px] py-3.5 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $u)
                            <tr class="border-b border-line last:border-b-0">
                                <td class="px-[22px] py-3.5">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-semibold text-ink">{{ $u->name }}</span>
                                        @if ($u->id === $me)<span class="rounded-md bg-primary-soft px-1.5 py-0.5 text-[11px] font-semibold text-primary">você</span>@endif
                                    </div>
                                    <div class="font-mono text-xs text-ink-muted">{{ $u->email }}</div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <x-ui.badge :tone="$roleTones[$u->role->value]" :icon="$roleIcons[$u->role->value]">{{ $u->role->label() }}</x-ui.badge>
                                        <div class="flex overflow-hidden rounded-lg border border-line-strong">
                                            @foreach (['user' => 'Usuário', 'admin' => 'Admin'] as $val => $lbl)
                                                <button wire:click="setRole({{ $u->id }}, '{{ $val }}')"
                                                        @class([
                                                            'px-2.5 py-1 text-xs font-semibold cursor-pointer transition-colors',
                                                            'bg-primary text-onprimary' => $u->role->value === $val,
                                                            'bg-surface text-ink-muted hover:bg-surface-2' => $u->role->value !== $val,
                                                        ])>{{ $lbl }}</button>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                                <td class="px-[22px] py-3.5">
                                    <div class="flex justify-end">
                                        @if ($u->id !== $me)
                                            <button wire:click="deleteUser({{ $u->id }})" wire:confirm="Remover {{ $u->name }} desta organização?" title="Remover"
                                                    class="flex h-9 w-9 items-center justify-center rounded-[9px] border border-crit bg-transparent text-crit transition-colors hover:bg-crit hover:text-crit-soft cursor-pointer"><x-ui.icon name="delete" :size="18" /></button>
                                        @else
                                            <span class="text-xs text-ink-muted">—</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
</div>
