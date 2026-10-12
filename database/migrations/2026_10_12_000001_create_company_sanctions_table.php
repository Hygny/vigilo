<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Conjunto de sanções CONHECIDAS de cada empresa monitorada (CGU: CEIS/CNEP/
 * CEPIM). É o estado contra o qual a varredura diff para detectar sanção NOVA —
 * que vira alerta. `fingerprint` identifica uma sanção distinta de forma estável
 * (não inclui a data de fim, para uma prorrogação não contar como sanção nova).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_sanctions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('monitored_company_id')->constrained()->cascadeOnDelete();
            $table->string('fonte'); // ceis / cnep / cepim
            $table->string('fingerprint');
            $table->text('tipo')->nullable();
            $table->text('orgao')->nullable();
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->timestamp('detected_at'); // quando NÓS a vimos pela 1ª vez
            $table->timestamps();

            // Uma sanção (por fingerprint) aparece uma única vez por empresa —
            // garante a deduplicação mesmo sob concorrência.
            $table->unique(['monitored_company_id', 'fingerprint']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_sanctions');
    }
};
