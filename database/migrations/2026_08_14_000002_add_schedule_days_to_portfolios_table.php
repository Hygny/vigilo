<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            // Dias do mês (1–31) em que este portfólio roda seus refreshes.
            // JSON com vários dias; null = sem agendamento.
            $table->json('schedule_days')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropColumn('schedule_days');
        });
    }
};
