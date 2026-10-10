<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Vincula change_events.triaged_by_id a users com onDelete(null): se o usuário
 * que triou for removido, a trilha fica com triaged_by_id = null em vez de
 * apontar para um id órfão. O SQLite (testes) não adiciona FK em tabela
 * existente via ALTER — é uma garantia de integridade do MySQL de produção.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // Nula referências órfãs (usuário já removido) antes de criar a FK —
        // senão o ALTER ADD FOREIGN KEY aborta.
        DB::table('change_events')
            ->whereNotNull('triaged_by_id')
            ->whereNotIn('triaged_by_id', DB::table('users')->select('id'))
            ->update(['triaged_by_id' => null]);

        Schema::table('change_events', function (Blueprint $table): void {
            $table->foreign('triaged_by_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('change_events', function (Blueprint $table): void {
            $table->dropForeign(['triaged_by_id']);
        });
    }
};
