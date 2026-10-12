<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dívida ativa da União (PGFN) das empresas da CARTEIRA. O dump oficial tem ~6M
 * inscrições (gigabytes); o importador filtra e guarda só os CNPJs monitorados,
 * então a tabela fica pequena e vive no banco do app. Uma empresa pode ter várias
 * inscrições (linhas). `tipo` = categoria do arquivo (previdenciário/não-prev/FGTS).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pgfn_debts', function (Blueprint $table): void {
            $table->id();
            $table->string('documento')->index(); // CNPJ (só dígitos)
            $table->string('tipo');                // previdenciario | nao-previdenciario | fgts | geral
            $table->string('situacao')->nullable();
            $table->string('receita')->nullable();
            $table->decimal('valor', 15, 2)->nullable();
            $table->date('data_inscricao')->nullable();
            $table->timestamp('imported_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pgfn_debts');
    }
};
