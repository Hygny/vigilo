<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cópia local do Registro Aeronáutico Brasileiro (ANAC, dados abertos —
 * `dados_aeronaves.csv`, atualizado todo dia): aeronaves e seus proprietários e
 * operadores. Serve o card "Bens e recebíveis" (recuperação de bens): por CNPJ
 * (vínculo certo) e por nome de pessoa (vínculo POSSÍVEL — a ANAC mascara o CPF
 * de forma diferente da Receita; o documento de PF nem é guardado, porque
 * juntar as duas máscaras revelaria o CPF inteiro).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aeronaves', function (Blueprint $table): void {
            $table->string('marca', 10)->primary();
            $table->string('modelo', 120)->nullable();
            $table->string('fabricante', 120)->nullable();
            $table->unsignedSmallInteger('ano_fabricacao')->nullable();
            $table->string('gravame', 120)->nullable();
            $table->date('data_matricula')->nullable();
            $table->date('data_cancelamento')->nullable();
            $table->string('motivo_cancelamento', 120)->nullable();
        });

        Schema::create('aeronave_vinculos', function (Blueprint $table): void {
            $table->id();
            $table->string('marca', 10)->index();
            $table->string('papel', 12); // proprietario | operador
            $table->string('tipo', 2); // PJ | PF
            $table->char('cnpj', 14)->nullable();
            $table->char('cnpj_basico', 8)->nullable()->index();
            $table->string('nome', 160)->nullable(); // só PJ (LGPD: de PF fica só o nome_norm)
            $table->string('nome_norm', 160)->index(); // NomeSocio::norm — p/ PF por nome
            $table->decimal('percentual', 6, 2)->nullable();
            $table->string('uf', 2)->nullable();
        });

        Schema::create('aeronave_cargas', function (Blueprint $table): void {
            $table->id();
            $table->date('data_arquivo');
            $table->unsignedInteger('aeronaves');
            $table->timestamp('carregado_em');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aeronave_cargas');
        Schema::dropIfExists('aeronave_vinculos');
        Schema::dropIfExists('aeronaves');
    }
};
