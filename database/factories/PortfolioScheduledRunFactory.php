<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Portfolio;
use App\Models\PortfolioScheduledRun;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PortfolioScheduledRun>
 */
class PortfolioScheduledRunFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'portfolio_id' => Portfolio::factory(),
            'scheduled_day' => fake()->numberBetween(1, 28),
            'ran_on' => now()->toDateString(),
            'companies_count' => fake()->numberBetween(0, 50),
            'dispatched_count' => fake()->numberBetween(0, 50),
        ];
    }
}
