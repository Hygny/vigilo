<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ChangeType;
use App\Enums\Severity;
use App\Models\ChangeEvent;
use App\Models\CompanySnapshot;
use App\Models\MonitoredCompany;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChangeEvent>
 */
class ChangeEventFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'monitored_company_id' => MonitoredCompany::factory(),
            'from_snapshot_id' => null,
            'to_snapshot_id' => CompanySnapshot::factory(),
            'type' => ChangeType::SituacaoChanged,
            'field' => 'situacao_cadastral',
            'old_value' => 'ATIVA',
            'new_value' => 'BAIXADA',
            'severity' => Severity::Critical,
            'detected_at' => now(),
            'acknowledged_at' => null,
        ];
    }

    public function acknowledged(): static
    {
        return $this->state(fn (array $attributes): array => [
            'acknowledged_at' => now(),
        ]);
    }
}
