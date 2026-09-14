<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Trilha de auditoria de personificações (super-admin "logar como").
        // E-mails denormalizados para o log continuar legível mesmo se o usuário
        // for excluído depois — por isso sem FK/cascade.
        Schema::create('impersonation_logs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('impersonator_id');
            $table->string('impersonator_email');
            $table->unsignedBigInteger('impersonated_user_id');
            $table->string('impersonated_email');
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('impersonator_id');
            $table->index('impersonated_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impersonation_logs');
    }
};
