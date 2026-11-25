<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cópia local dos cadastros de sanções da CGU (CEIS, CNEP, CEPIM), baixados
 * todo dia do Portal da Transparência pelo `vigilo:sancoes-cgu`. Só pessoa
 * jurídica (CNPJ de 14 díg) — serve para checar o risco de QUALQUER empresa
 * (perfil público e empresas ligadas) sem uma chamada à API por empresa.
 *
 * `cgu_cargas`: qual arquivo (data) de cada fonte está carregado — a tela diz
 * de quando é a cópia e nunca "sem sanção" se a fonte nunca foi carregada.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cgu_sancoes', function (Blueprint $table): void {
            $table->id();
            $table->string('fonte', 8)->index();
            $table->string('codigo', 40)->nullable();
            $table->char('cnpj', 14);
            $table->char('cnpj_basico', 8)->index();
            $table->text('nome')->nullable();
            $table->text('categoria')->nullable();
            $table->text('orgao')->nullable();
            $table->string('uf_orgao', 2)->nullable();
            $table->string('esfera', 40)->nullable();
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->decimal('valor_multa', 16, 2)->nullable();
            $table->string('processo', 120)->nullable();
        });

        Schema::create('cgu_cargas', function (Blueprint $table): void {
            $table->string('fonte', 8)->primary();
            $table->date('data_arquivo');
            $table->unsignedInteger('registros');
            $table->timestamp('carregado_em');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cgu_cargas');
        Schema::dropIfExists('cgu_sancoes');
    }
};
