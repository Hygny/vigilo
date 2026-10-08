<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Uma linha por rodada de manutenção mensal da base CNPJ (reimport +
        // re-coleta + normalização). O super-admin vê em /admin a data da última
        // execução, o status de cada passo e eventual erro.
        Schema::create('maintenance_runs', function (Blueprint $table): void {
            $table->id();
            $table->string('kind')->default('cnpj_mensal');
            $table->string('status'); // success | failed
            $table->string('reimport_status')->nullable();   // ok | fail | skip
            $table->string('recoleta_status')->nullable();
            $table->string('normalizar_status')->nullable();
            $table->text('message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['kind', 'finished_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_runs');
    }
};
