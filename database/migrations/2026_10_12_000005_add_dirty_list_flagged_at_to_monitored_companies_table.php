<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Marca que a empresa está na Lista Suja do trabalho escravo (estado conhecido).
 * Null = não consta; preenchida = consta. A transição não-consta → consta num
 * refresh é o gatilho do alerta "entrou na Lista Suja".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitored_companies', function (Blueprint $table): void {
            $table->timestamp('dirty_list_flagged_at')->nullable()->after('sanctions_synced_at');
        });
    }

    public function down(): void
    {
        Schema::table('monitored_companies', function (Blueprint $table): void {
            $table->dropColumn('dirty_list_flagged_at');
        });
    }
};
