<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('change_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitored_company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_snapshot_id')->nullable()->constrained('company_snapshots')->nullOnDelete();
            $table->foreignId('to_snapshot_id')->constrained('company_snapshots')->cascadeOnDelete();
            $table->enum('type', [
                'situacao_changed',
                'partner_added',
                'partner_removed',
                'address_changed',
                'cnae_changed',
                'porte_changed',
                'name_changed',
            ]);
            $table->string('field');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->enum('severity', ['critical', 'high', 'medium', 'low']);
            $table->timestamp('detected_at');
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();

            $table->index(['monitored_company_id', 'detected_at']);
            $table->index('acknowledged_at');
            $table->index('severity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('change_events');
    }
};
