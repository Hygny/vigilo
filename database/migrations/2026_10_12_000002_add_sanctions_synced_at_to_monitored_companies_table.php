<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Marca a 1ª sincronização de sanções da empresa. Null = nunca sincronizada → a
 * próxima sync é BASELINE (grava o que existe sem alertar; sanção pré-existente
 * não é "novidade"). Preenchida → sync normal, só sanção nova vira alerta.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitored_companies', function (Blueprint $table): void {
            $table->timestamp('sanctions_synced_at')->nullable()->after('last_refresh_error');
        });
    }

    public function down(): void
    {
        Schema::table('monitored_companies', function (Blueprint $table): void {
            $table->dropColumn('sanctions_synced_at');
        });
    }
};
