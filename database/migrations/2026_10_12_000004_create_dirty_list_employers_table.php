<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * "Lista Suja" do trabalho escravo (Cadastro de Empregadores que submeteram
 * trabalhadores a condições análogas à de escravo — MTE/Portaria). É dado
 * público, republicado inteiro a cada atualização (~2×/ano), então a importação
 * é recarga total. `documento` guarda só os dígitos (CNPJ/CPF) para casar com o
 * CNPJ da empresa monitorada. Vive no banco do app (lista pequena), não na base
 * CNPJ do pipeline.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dirty_list_employers', function (Blueprint $table): void {
            $table->id();
            $table->string('documento')->index(); // CNPJ/CPF do empregador (só dígitos)
            $table->text('nome')->nullable();
            $table->string('uf', 8)->nullable();
            $table->string('ano', 16)->nullable();
            $table->text('estabelecimento')->nullable();
            $table->timestamp('imported_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dirty_list_employers');
    }
};
