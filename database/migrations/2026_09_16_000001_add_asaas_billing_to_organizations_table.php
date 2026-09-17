<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Vínculos com o Asaas e status da cobrança. Colunas sensíveis: definidas
        // só pelo fluxo de cobrança (forceFill via SubscriptionService), fora do
        // $fillable. O webhook localiza a org pelo asaas_subscription_id — daí o
        // índice.
        Schema::table('organizations', function (Blueprint $table): void {
            $table->string('asaas_customer_id')->nullable()->after('plan');
            $table->string('asaas_subscription_id')->nullable()->after('asaas_customer_id');
            $table->string('billing_status')->default('none')->after('asaas_subscription_id');

            $table->index('asaas_subscription_id');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table): void {
            $table->dropIndex(['asaas_subscription_id']);
            $table->dropColumn(['asaas_customer_id', 'asaas_subscription_id', 'billing_status']);
        });
    }
};
