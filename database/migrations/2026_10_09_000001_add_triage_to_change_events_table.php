<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Triagem do alerta (novo → em análise → descartado | virou caso).
        // triaged_by_id é referência "solta" (sem FK) ao usuário que triou — o
        // log continua legível mesmo se o usuário for excluído (mesmo padrão de
        // impersonation_logs).
        Schema::table('change_events', function (Blueprint $table): void {
            $table->string('triage_status')->default('novo');
            $table->text('triage_reason')->nullable();
            $table->timestamp('triaged_at')->nullable();
            $table->unsignedBigInteger('triaged_by_id')->nullable();

            $table->index('triage_status');
        });

        // Backfill: o que já foi "reconhecido" vira "em análise" (não perde o
        // histórico de que alguém já tinha olhado).
        DB::table('change_events')
            ->whereNotNull('acknowledged_at')
            ->update(['triage_status' => 'em_analise']);
    }

    public function down(): void
    {
        Schema::table('change_events', function (Blueprint $table): void {
            $table->dropIndex(['triage_status']);
            $table->dropColumn(['triage_status', 'triage_reason', 'triaged_at', 'triaged_by_id']);
        });
    }
};
