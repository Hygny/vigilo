<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitored_company_id')->constrained()->cascadeOnDelete();
            $table->timestamp('captured_at');
            $table->string('razao_social');
            $table->string('nome_fantasia')->nullable();
            $table->string('situacao_cadastral');
            $table->date('situacao_data')->nullable();
            $table->string('cnae_principal')->nullable();
            $table->string('porte')->nullable();
            $table->string('natureza_juridica')->nullable();
            $table->string('logradouro')->nullable();
            $table->string('municipio')->nullable();
            $table->char('uf', 2)->nullable();
            $table->json('raw_json');
            $table->timestamps();

            $table->index(['monitored_company_id', 'captured_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_snapshots');
    }
};
