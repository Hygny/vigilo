<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitored_companies', function (Blueprint $table) {
            // Acelera o filtro por status e as contagens (groupBy) dentro de um
            // portfólio grande.
            $table->index(['portfolio_id', 'last_refresh_status']);
        });
    }

    public function down(): void
    {
        Schema::table('monitored_companies', function (Blueprint $table) {
            $table->dropIndex(['portfolio_id', 'last_refresh_status']);
        });
    }
};
