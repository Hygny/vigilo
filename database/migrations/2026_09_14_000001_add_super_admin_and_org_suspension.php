<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Super-admin (dono da plataforma) — enxerga/gere todas as organizações.
        // Fora do #[Fillable] do User de propósito: só definido via seeder/tinker
        // ou ação explícita, nunca por mass assignment.
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('is_super_admin')->default(false)->after('role');
        });

        // Suspensão de organização (ex.: inadimplência): quando preenchido,
        // bloqueia o login/uso dos usuários da org (super-admin é isento).
        Schema::table('organizations', function (Blueprint $table): void {
            $table->timestamp('suspended_at')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('is_super_admin');
        });

        Schema::table('organizations', function (Blueprint $table): void {
            $table->dropColumn('suspended_at');
        });
    }
};
