<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MonitoredCompany;
use App\Models\Portfolio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MonitoredCompany>
 */
class MonitoredCompanyFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'portfolio_id' => Portfolio::factory(),
            'cnpj' => fake()->numerify('##############'),
            'label' => fake()->optional()->company(),
            'last_refreshed_at' => null,
        ];
    }
}
