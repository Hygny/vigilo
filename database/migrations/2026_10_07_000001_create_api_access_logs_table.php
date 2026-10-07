<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Trilha de auditoria da API OSINT de vigilância (LGPD): quem (token/
        // usuário), o quê (método/rota), com quais parâmetros e qual resultado.
        // Os nomes consultados no POST /empresas/por-socio ficam em `params`.
        Schema::create('api_access_logs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('token_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('method', 10);
            $table->string('path');
            $table->json('params')->nullable();
            $table->unsignedSmallInteger('status')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('token_id');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_access_logs');
    }
};
