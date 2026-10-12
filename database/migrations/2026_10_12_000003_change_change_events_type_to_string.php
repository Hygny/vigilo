<?php

declare(strict_types=1);

use App\Enums\ChangeType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `change_events.type` era um ENUM com a lista fixa dos tipos de mudança — o que
 * obrigava uma migração a cada novo tipo. O conjunto agora cresce com os sinais
 * de risco (a 1ª: `sancao_adicionada`), então vira `string`. A fonte de verdade
 * dos valores válidos passa a ser o enum PHP {@see ChangeType} (cast
 * no model). A `severity` segue ENUM (conjunto pequeno e estável).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('change_events', function (Blueprint $table): void {
            $table->string('type')->change();
        });
    }

    public function down(): void
    {
        Schema::table('change_events', function (Blueprint $table): void {
            $table->enum('type', [
                'situacao_changed',
                'partner_added',
                'partner_removed',
                'address_changed',
                'cnae_changed',
                'porte_changed',
                'name_changed',
            ])->change();
        });
    }
};
