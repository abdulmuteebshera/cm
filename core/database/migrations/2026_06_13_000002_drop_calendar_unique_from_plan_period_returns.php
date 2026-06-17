<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plan_period_returns', function (Blueprint $table): void {
            $table->dropUnique('plan_period_returns_plan_id_year_period_index_unique');
        });
    }

    public function down(): void
    {
        Schema::table('plan_period_returns', function (Blueprint $table): void {
            $table->unique(['plan_id', 'year', 'period_index'], 'plan_period_returns_plan_id_year_period_index_unique');
        });
    }
};
