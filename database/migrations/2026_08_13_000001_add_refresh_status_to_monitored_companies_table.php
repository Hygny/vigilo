<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitored_companies', function (Blueprint $table) {
            $table->string('last_refresh_status')->nullable()->after('last_refreshed_at');
            $table->text('last_refresh_error')->nullable()->after('last_refresh_status');
        });
    }

    public function down(): void
    {
        Schema::table('monitored_companies', function (Blueprint $table) {
            $table->dropColumn(['last_refresh_status', 'last_refresh_error']);
        });
    }
};
