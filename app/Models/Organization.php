<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BillingStatus;
use App\Enums\Plan;
use Database\Factories\OrganizationFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property Plan $plan
 * @property string|null $asaas_customer_id
 * @property string|null $asaas_subscription_id
 * @property BillingStatus $billing_status
 * @property Carbon|null $suspended_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, User> $users
 * @property-read Collection<int, Portfolio> $portfolios
 */
class Organization extends Model
{
    /** @use HasFactory<OrganizationFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = ['name'];

    /**
     * Default em memória: garante que `plan` e `billing_status` nunca sejam null
     * em instâncias novas (o padrão do banco também é 'free'/'none'). Ambos ficam
     * fora do $fillable — definidos só por ação do super-admin ou do fluxo de
     * cobrança (forceFill).
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'plan' => 'free',
        'billing_status' => 'none',
    ];

    /**
     * Organização suspensa (ex.: inadimplência) bloqueia login/uso dos seus
     * usuários. `suspended_at` é definido só por ação do super-admin, nunca por
     * mass assignment (fora do $fillable de propósito).
     */
    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }

    /**
     * Já possui uma assinatura recorrente vinculada no Asaas.
     */
    public function hasSubscription(): bool
    {
        return $this->asaas_subscription_id !== null;
    }

    /**
     * Cobrança vencida (inadimplente). A suspensão de acesso é aplicada à parte
     * (suspended_at), mas anda junto na prática.
     */
    public function isPastDue(): bool
    {
        return $this->billing_status === BillingStatus::PastDue;
    }

    /**
     * Teto de CNPJs monitorados do plano atual.
     */
    public function maxMonitoredCompanies(): int
    {
        return $this->plan->maxCompanies();
    }

    /**
     * Quantos CNPJs a organização monitora hoje (somando todos os portfólios).
     */
    public function monitoredCompaniesCount(): int
    {
        $portfolioIds = $this->portfolios()->withoutGlobalScopes()->pluck('id');

        return MonitoredCompany::query()->whereIn('portfolio_id', $portfolioIds)->count();
    }

    /**
     * Vagas de CNPJ restantes no plano (nunca negativo).
     */
    public function remainingCompanySlots(): int
    {
        return max(0, $this->maxMonitoredCompanies() - $this->monitoredCompaniesCount());
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'plan' => Plan::class,
            'billing_status' => BillingStatus::class,
            'suspended_at' => 'datetime',
        ];
    }

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return HasMany<Portfolio, $this>
     */
    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class);
    }
}
