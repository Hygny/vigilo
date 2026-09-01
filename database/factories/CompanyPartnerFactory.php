<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CompanyPartner;
use App\Models\CompanySnapshot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompanyPartner>
 */
class CompanyPartnerFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_snapshot_id' => CompanySnapshot::factory(),
            'documento' => fake()->numerify('###########'),
            'nome' => (string) fake()->name(),
            'qualificacao' => fake()->randomElement(['Sócio-Administrador', 'Sócio', 'Administrador']),
        ];
    }
}
