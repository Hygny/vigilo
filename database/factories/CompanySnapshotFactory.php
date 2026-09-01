<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CompanySnapshot;
use App\Models\MonitoredCompany;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompanySnapshot>
 */
class CompanySnapshotFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'monitored_company_id' => MonitoredCompany::factory(),
            'captured_at' => now(),
            'razao_social' => (string) fake()->company(),
            'nome_fantasia' => fake()->optional()->companySuffix(),
            'situacao_cadastral' => 'ATIVA',
            'situacao_data' => null,
            'cnae_principal' => (string) fake()->numerify('#######'),
            'porte' => fake()->randomElement(['MEI', 'ME', 'EPP', 'DEMAIS']),
            'natureza_juridica' => 'Sociedade Empresária Limitada',
            'logradouro' => fake()->streetAddress(),
            'municipio' => fake()->city(),
            'uf' => fake()->randomElement(['SP', 'RJ', 'MG', 'RS', 'PR']),
            'raw_json' => [],
        ];
    }
}
