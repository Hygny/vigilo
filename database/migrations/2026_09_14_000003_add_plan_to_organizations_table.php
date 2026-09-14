<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Plano de assinatura da organização (App\Enums\Plan). Define a quota de
        // CNPJs monitorados. Definido só por ação do super-admin — fora do
        // $fillable de propósito.
        Schema::table('organizations', function (Blueprint $table): void {
            $table->string('plan')->default('free')->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table): void {
            $table->dropColumn('plan');
        });
    }
};
