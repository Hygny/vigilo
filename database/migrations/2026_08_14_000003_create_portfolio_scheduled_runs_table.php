<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_scheduled_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('scheduled_day'); // dia (1–31) que disparou
            $table->date('ran_on');
            $table->unsignedInteger('companies_count')->default(0);
            $table->unsignedInteger('dispatched_count')->default(0);
            $table->timestamps();

            // Idempotência: no máximo uma execução por portfólio por dia.
            $table->unique(['portfolio_id', 'ran_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_scheduled_runs');
    }
};
