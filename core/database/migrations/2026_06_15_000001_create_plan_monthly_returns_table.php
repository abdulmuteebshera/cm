<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_monthly_returns', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->decimal('return_percent', 8, 4)->default(0);
            $table->string('payout_status', 20)->default('draft');
            $table->decimal('total_payout', 28, 8)->default(0);
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->unique(['plan_id', 'year', 'month']);
            $table->index(['plan_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_monthly_returns');
    }
};
