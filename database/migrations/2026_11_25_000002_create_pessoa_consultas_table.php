<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Registro (LGPD — prestação de contas) de cada busca de pessoa e de cada
 * página de pessoa aberta: quem, de qual organização, quando e o quê (o nome
 * digitado ou a chave opaca da pessoa). Append-only; podado pela retenção
 * `cnpj.pessoas.retencao_dias`. Referências soltas (sem FK): o registro
 * sobrevive à remoção do usuário.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pessoa_consultas', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('organization_id')->nullable()->index();
            $table->string('tipo', 10); // busca | perfil
            $table->string('termo', 160);
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pessoa_consultas');
    }
};
