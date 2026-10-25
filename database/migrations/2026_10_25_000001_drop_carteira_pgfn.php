<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A dívida ativa (PGFN) passou a vir da base CNPJ — a PGFN INTEIRA na tabela
 * `pgfn_dividas` da VM (deploy/cnpj-db/bin/pgfn.sh), consultada para qualquer
 * CNPJ. Sai a cópia filtrada pela carteira no banco do app (`pgfn_debts`) e a
 * marca do refresh incremental (`pgfn_synced_at`). Ambas eram dado derivado do
 * dump — nada se perde.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('pgfn_debts');

        if (Schema::hasColumn('monitored_companies', 'pgfn_synced_at')) {
            Schema::table('monitored_companies', function (Blueprint $table): void {
                $table->dropColumn('pgfn_synced_at');
            });
        }
    }

    public function down(): void
    {
        Schema::table('monitored_companies', function (Blueprint $table): void {
            $table->timestamp('pgfn_synced_at')->nullable()->after('dirty_list_flagged_at');
        });

        Schema::create('pgfn_debts', function (Blueprint $table): void {
            $table->id();
            $table->string('documento')->index();
            $table->string('tipo');
            $table->string('situacao')->nullable();
            $table->string('receita')->nullable();
            $table->decimal('valor', 15, 2)->nullable();
            $table->date('data_inscricao')->nullable();
            $table->timestamp('imported_at');
            $table->timestamps();
        });
    }
};
