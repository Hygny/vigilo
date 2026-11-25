<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Monitoramento de DEVEDORA (recuperação de crédito): empresa da carteira
 * marcada pelo usuário (`recuperacao_desde`), com a data da dívida opcional
 * (`recuperacao_marco`; null = 1ª inscrição na dívida ativa da União). A
 * varredura diária compara o que ela tem agora — aeronaves, participações,
 * contratos federais vigentes, possíveis sucessoras — com o conhecido
 * (`recuperacao_itens`): item novo vira alerta. A linha de base é POR TIPO
 * (`recuperacao_tipos` = tipos já sincronizados): um tipo cuja fonte nunca
 * respondeu completa é baseline (sem alerta) na 1ª vez que responder.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitored_companies', function (Blueprint $table): void {
            $table->timestamp('recuperacao_desde')->nullable()->after('neighbor_risk_synced_at')->index(); // filtro da varredura
            $table->date('recuperacao_marco')->nullable()->after('recuperacao_desde');
            $table->timestamp('recuperacao_synced_at')->nullable()->after('recuperacao_marco');
            $table->string('recuperacao_tipos', 100)->nullable()->after('recuperacao_synced_at');
        });

        Schema::create('recuperacao_itens', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('monitored_company_id')->constrained()->cascadeOnDelete();
            $table->string('tipo', 20); // aeronave | participacao | contrato | sucessora
            $table->string('chave', 40); // marca | raiz do CNPJ | id do contrato
            $table->timestamp('visto_em');
            // Rodadas seguidas sem o item (fonte encolheu de passagem): só é esquecido após algumas.
            $table->unsignedTinyInteger('ausencias')->default(0);
            $table->unique(['monitored_company_id', 'tipo', 'chave']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recuperacao_itens');

        Schema::table('monitored_companies', function (Blueprint $table): void {
            $table->dropIndex(['recuperacao_desde']);
            $table->dropColumn(['recuperacao_desde', 'recuperacao_marco', 'recuperacao_synced_at', 'recuperacao_tipos']);
        });
    }
};
