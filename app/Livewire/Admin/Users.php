<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Gestão de usuários da organização — apenas administradores. Cada admin vê e
 * gerencia somente os usuários da própria organização (multi-tenant).
 */
#[Layout('layouts.app')]
class Users extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $role = 'user';

    public function mount(): void
    {
        abort_unless($this->currentUser()->isAdmin(), 403);
    }

    public function createUser(): void
    {
        $admin = $this->currentUser();
        abort_unless($admin->isAdmin(), 403);

        $validated = $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', Password::defaults()],
            'role' => ['required', Rule::enum(Role::class)],
        ]);

        // forceFill: email_verified_at está fora do #[Fillable] do User; via
        // mass assignment seria descartado e o novo usuário cairia no middleware
        // 'verified' sem conseguir usar o app.
        (new User)->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'organization_id' => $admin->organization_id,
            'role' => $validated['role'],
            'email_verified_at' => now(),
        ])->save();

        $this->reset('name', 'email', 'password');
        $this->role = 'user';
        session()->flash('status', 'Usuário criado.');
    }

    public function setRole(int $userId, string $role): void
    {
        abort_unless($this->currentUser()->isAdmin(), 403);

        $newRole = Role::tryFrom($role);

        if ($newRole === null) {
            return;
        }

        $target = $this->orgUsers()->findOrFail($userId);

        DB::transaction(function () use ($target, $newRole): void {
            // Reconta os admins DENTRO da transação, travando as linhas: evita que
            // dois rebaixamentos concorrentes zerem o último administrador (TOCTOU).
            if ($target->isAdmin() && $newRole !== Role::Admin && $this->lockedAdminCount() <= 1) {
                $this->addError('role', 'A organização precisa de ao menos um administrador.');

                return;
            }

            // role está fora do #[Fillable] → atribuição explícita via forceFill.
            $target->forceFill(['role' => $newRole])->save();
            session()->flash('status', 'Papel atualizado.');
        });
    }

    public function deleteUser(int $userId): void
    {
        $admin = $this->currentUser();
        abort_unless($admin->isAdmin(), 403);

        $target = $this->orgUsers()->findOrFail($userId);

        if ($target->id === $admin->id) {
            $this->addError('delete', 'Você não pode excluir a própria conta por aqui.');

            return;
        }

        DB::transaction(function () use ($target): void {
            if ($target->isAdmin() && $this->lockedAdminCount() <= 1) {
                $this->addError('delete', 'A organização precisa de ao menos um administrador.');

                return;
            }

            $target->delete();
            session()->flash('status', 'Usuário removido.');
        });
    }

    public function render(): View
    {
        return view('livewire.admin.users', [
            'users' => $this->orgUsers()->orderByDesc('id')->get(),
        ]);
    }

    private function currentUser(): User
    {
        /** @var User $user */
        $user = auth()->user();

        return $user;
    }

    /**
     * @return Builder<User>
     */
    private function orgUsers(): Builder
    {
        return User::query()->where('organization_id', $this->currentUser()->organization_id);
    }

    /** Admins da org, contados com lock (dentro de transação) — guarda TOCTOU. */
    private function lockedAdminCount(): int
    {
        return (int) $this->orgUsers()->where('role', Role::Admin->value)->lockForUpdate()->count();
    }
}
