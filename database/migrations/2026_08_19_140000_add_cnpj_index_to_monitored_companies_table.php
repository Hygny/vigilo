<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A API pública (GET /api/cnpj/{cnpj}) filtra por `cnpj` isolado e escopa a
     * organização por subquery — o único índice existente é o composto
     * unique(portfolio_id, cnpj), cuja coluna líder é portfolio_id, então um
     * predicado só em `cnpj` não faz seek. Este índice cobre o hot-path.
     */
    public function up(): void
    {
        Schema::table('monitored_companies', function (Blueprint $table): void {
            $table->index('cnpj');
        });
    }

    public function down(): void
    {
        Schema::table('monitored_companies', function (Blueprint $table): void {
            $table->dropIndex(['cnpj']);
        });
    }
};
