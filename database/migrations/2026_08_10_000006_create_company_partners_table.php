<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_snapshot_id')->constrained()->cascadeOnDelete();
            $table->string('documento')->nullable();
            $table->string('nome');
            $table->string('qualificacao')->nullable();
            $table->timestamps();

            // GANCHO V2: matéria-prima do grafo de sócios — busca por documento.
            $table->index('documento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_partners');
    }
};
