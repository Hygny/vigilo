<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * "Risco ao redor" monitorado: o conjunto conhecido de sinais de risco de cada
 * empresa LIGADA a uma empresa da carteira (raiz do CNPJ → códigos de sinal).
 * A varredura diária compara com o atual: sinal novo numa ligada vira alerta.
 * `neighbor_risk_synced_at` null = 1ª varredura é baseline (sem alerta).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_neighbor_flags', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('monitored_company_id')->constrained()->cascadeOnDelete();
            $table->char('cnpj_basico', 8);
            $table->string('sinais', 80); // códigos ordenados, separados por vírgula
            $table->timestamp('visto_em');
            // Rodadas seguidas em que a ligada NÃO apareceu (grafo encolheu de passagem:
            // base em recarga, teto da reversa). Só é esquecida após algumas.
            $table->unsignedTinyInteger('ausencias')->default(0);
            $table->unique(['monitored_company_id', 'cnpj_basico']);
        });

        Schema::table('monitored_companies', function (Blueprint $table): void {
            $table->timestamp('neighbor_risk_synced_at')->nullable()->after('dirty_list_flagged_at');
        });
    }

    public function down(): void
    {
        Schema::table('monitored_companies', function (Blueprint $table): void {
            $table->dropColumn('neighbor_risk_synced_at');
        });

        Schema::dropIfExists('company_neighbor_flags');
    }
};
