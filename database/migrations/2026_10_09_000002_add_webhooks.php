<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Webhook de saída por organização: URL + segredo (criptografado no app).
        // Ficam fora do $fillable — definidos só pela tela de Integrações (admin).
        Schema::table('organizations', function (Blueprint $table): void {
            $table->string('webhook_url', 2048)->nullable();
            $table->text('webhook_secret')->nullable();
        });

        // Trilha de entregas do webhook (observabilidade na tela de Integrações).
        Schema::create('webhook_deliveries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->uuid('event_id')->index();
            $table->string('event_type')->default('company.changes');
            $table->string('cnpj')->nullable();
            $table->unsignedInteger('changes_count')->default(0);
            $table->string('status'); // success | failed
            $table->unsignedSmallInteger('response_status')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['organization_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_deliveries');

        Schema::table('organizations', function (Blueprint $table): void {
            $table->dropColumn(['webhook_url', 'webhook_secret']);
        });
    }
};
