<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;
use App\Support\Cnpj;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed a demo organization with one user and a portfolio of real CNPJs so
     * the app can be exercised end-to-end (run the refresh to populate data).
     */
    public function run(): void
    {
        $organization = Organization::create(['name' => 'Vigilo Demo']);

        User::create([
            'name' => 'Demo Vigilo',
            'email' => 'demo@vigilo.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'organization_id' => $organization->id,
        ]);

        $portfolio = Portfolio::create([
            'organization_id' => $organization->id,
            'name' => 'Carteira demo',
        ]);

        // Real, publicly-registered CNPJs (PJ data is open) — used only to
        // demonstrate monitoring. Run `php artisan queue:work` and click
        // "Atualizar" to populate snapshots from BrasilAPI.
        $companies = [
            '00.000.000/0001-91' => 'Banco do Brasil',
            '33.000.167/0001-01' => 'Petrobras',
            '60.701.190/0001-04' => 'Itaú Unibanco',
            '47.960.950/0001-21' => 'Magazine Luiza',
            '07.526.557/0001-00' => 'Ambev',
        ];

        foreach ($companies as $cnpj => $label) {
            $portfolio->monitoredCompanies()->create([
                'cnpj' => Cnpj::fromString($cnpj)->value,
                'label' => $label,
            ]);
        }
    }
}
