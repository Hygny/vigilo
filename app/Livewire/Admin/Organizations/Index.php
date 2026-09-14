<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Organizations;

use App\Enums\Role;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
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
 * Back-office do super-admin: lista todas as organizações com uso e permite
 * onboardar uma nova (organização + primeiro administrador). Opera fora do
 * escopo de tenant — todo método público re-checa isSuperAdmin().
 */
#[Layout('layouts.admin')]
class Index extends Component
{
    public string $orgName = '';

    public string $adminName = '';

    public string $adminEmail = '';

    public string $adminPassword = '';

    public function mount(): void
    {
        abort_unless($this->currentUser()->isSuperAdmin(), 403);
    }

    public function createOrganization(): void
    {
        abort_unless($this->currentUser()->isSuperAdmin(), 403);

        $validated = $this->validate([
            'orgName' => ['required', 'string', 'min:2', 'max:255'],
            'adminName' => ['required', 'string', 'min:2', 'max:255'],
            'adminEmail' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')],
            'adminPassword' => ['required', 'string', Password::defaults()],
        ]);

        DB::transaction(function () use ($validated): void {
            $organization = Organization::create(['name' => $validated['orgName']]);

            // forceFill: email_verified_at fica fora do #[Fillable] do User, então
            // mass assignment o descartaria e o admin cairia no middleware 'verified'.
            (new User)->forceFill([
                'name' => $validated['adminName'],
                'email' => $validated['adminEmail'],
                'password' => Hash::make($validated['adminPassword']),
                'organization_id' => $organization->id,
                'role' => Role::Admin,
                'email_verified_at' => now(),
            ])->save();
        });

        $this->reset('orgName', 'adminName', 'adminEmail', 'adminPassword');
        session()->flash('status', 'Organização criada com administrador.');
    }

    public function render(): View
    {
        // Contagem de empresas por organização em uma única query. MonitoredCompany
        // não é globalmente escopado, então isto enxerga todos os tenants.
        $companyCounts = MonitoredCompany::query()
            ->join('portfolios', 'portfolios.id', '=', 'monitored_companies.portfolio_id')
            ->selectRaw('portfolios.organization_id as org_id, COUNT(*) as aggregate')
            ->groupBy('portfolios.organization_id')
            ->pluck('aggregate', 'org_id');

        $organizations = Organization::query()
            ->withCount([
                'users',
                // Portfolio é globalmente escopado; ignora o scope para contar
                // por organização independentemente do usuário logado.
                'portfolios' => fn (Builder $q) => $q->withoutGlobalScopes(),
            ])
            ->orderByDesc('id')
            ->get();

        return view('livewire.admin.organizations.index', [
            'organizations' => $organizations,
            'companyCounts' => $companyCounts,
        ]);
    }

    private function currentUser(): User
    {
        /** @var User $user */
        $user = auth()->user();

        return $user;
    }
}
