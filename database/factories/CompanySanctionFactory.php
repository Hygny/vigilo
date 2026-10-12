<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CompanySanction;
use App\Models\MonitoredCompany;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @extends Factory<CompanySanction>
 */
class CompanySanctionFactory extends Factory
{
    protected $model = CompanySanction::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'monitored_company_id' => MonitoredCompany::factory(),
            'fonte' => 'ceis',
            'fingerprint' => sha1(Str::uuid()->toString()),
            'tipo' => 'Empresa inidônea',
            'orgao' => 'TCU',
            'data_inicio' => '2023-01-01',
            'data_fim' => null,
            'detected_at' => Carbon::now(),
        ];
    }
}
