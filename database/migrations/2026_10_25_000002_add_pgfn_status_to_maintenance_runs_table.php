<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Passo 3 da manutenção semanal da base CNPJ: a carga da PGFN inteira na VM
 * (no-op enquanto o trimestre mais novo já estiver carregado). ok|fail|skip,
 * como os outros passos; nulo nas rodadas antigas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_runs', function (Blueprint $table): void {
            $table->string('pgfn_status')->nullable()->after('normalizar_status');
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_runs', function (Blueprint $table): void {
            $table->dropColumn('pgfn_status');
        });
    }
};
