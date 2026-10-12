<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Marca quando um CNPJ monitorado foi conferido contra o dump da PGFN. Nulo =
 * ainda não conferido (empresa recém-adicionada) → o refresh incremental
 * (deploy/refrescar-pgfn-novos.sh) pega esses e carrega a dívida deles a partir
 * do cache, sem esperar o reimport trimestral.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitored_companies', function (Blueprint $table): void {
            $table->timestamp('pgfn_synced_at')->nullable()->after('dirty_list_flagged_at');
        });
    }

    public function down(): void
    {
        Schema::table('monitored_companies', function (Blueprint $table): void {
            $table->dropColumn('pgfn_synced_at');
        });
    }
};
