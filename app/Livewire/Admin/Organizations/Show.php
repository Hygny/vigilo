<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Organizations;

use App\Enums\Role;
use App\Models\ImpersonationLog;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Detalhe de uma organização no back-office: usuários, portfólios e uso, com as
 * ações do super-admin — suspender/reativar a org e promover/rebaixar papéis.
 * Todo método público re-checa isSuperAdmin().
 */
#[Layout('layouts.admin')]
class Show extends Component
{
    public Organization $organization;

    public function mount(Organization $organization): void
    {
        abort_unless($this->currentUser()->isSuperAdmin(), 403);

        $this->organization = $organization;
    }

    public function suspend(): void
    {
        abort_unless($this->currentUser()->isSuperAdmin(), 403);

        if (! $this->organization->isSuspended()) {
            $this->organization->forceFill(['suspended_at' => Carbon::now()])->save();
        }

        session()->flash('status', 'Organização suspensa.');
    }

    public function reactivate(): void
    {
        abort_unless($this->currentUser()->isSuperAdmin(), 403);

        if ($this->organization->isSuspended()) {
            $this->organization->forceFill(['suspended_at' => null])->save();
        }

        session()->flash('status', 'Organização reativada.');
    }

    public function setRole(int $userId, string $role): void
    {
        abort_unless($this->currentUser()->isSuperAdmin(), 403);

        $newRole = Role::tryFrom($role);

        if ($newRole === null) {
            return;
        }

        /** @var User $target */
        $target = $this->organization->users()->findOrFail($userId);

        // Nunca deixar a organização sem administrador.
        if ($target->isAdmin() && $newRole !== Role::Admin && $this->adminCount() <= 1) {
            $this->addError('role', 'A organização precisa de ao menos um administrador.');

            return;
        }

        $target->update(['role' => $newRole]);
        session()->flash('status', 'Papel atualizado.');
    }

    /**
     * Personifica um usuário da org ("logar como"): registra a auditoria, guarda
     * o id do super-admin na sessão e troca a identidade. O retorno é pelo banner
     * (rota impersonate.stop). Redirect completo (navigate:false) para o app do
     * tenant carregar já com a nova identidade.
     */
    public function impersonate(int $userId): void
    {
        $admin = $this->currentUser();
        abort_unless($admin->isSuperAdmin(), 403);

        /** @var User $target */
        $target = $this->organization->users()->findOrFail($userId);

        // Nunca personificar outro super-admin nem a si mesmo.
        if ($target->isSuperAdmin() || $target->id === $admin->id) {
            return;
        }

        ImpersonationLog::create([
            'impersonator_id' => $admin->id,
            'impersonator_email' => $admin->email,
            'impersonated_user_id' => $target->id,
            'impersonated_email' => $target->email,
            'organization_id' => $this->organization->id,
        ]);

        session(['impersonator_id' => $admin->id]);
        Auth::login($target);

        $this->redirect(route('dashboard'), navigate: false);
    }

    public function render(): View
    {
        $users = $this->organization->users()->orderByDesc('id')->get();

        $portfolios = $this->organization->portfolios()
            ->withoutGlobalScopes()
            ->withCount('monitoredCompanies')
            ->orderByDesc('id')
            ->get();

        return view('livewire.admin.organizations.show', [
            'users' => $users,
            'portfolios' => $portfolios,
            'companiesTotal' => (int) $portfolios->sum('monitored_companies_count'),
        ]);
    }

    private function adminCount(): int
    {
        return $this->organization->users()->where('role', Role::Admin->value)->count();
    }

    private function currentUser(): User
    {
        /** @var User $user */
        $user = auth()->user();

        return $user;
    }
}
