<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plan_period_returns', function (Blueprint $table): void {
            $table->date('payout_date')->nullable()->after('period_end');
        });

        $rows = DB::table('plan_period_returns')->whereNull('payout_date')->get();

        foreach ($rows as $row) {
            $periodEnd = \Carbon\Carbon::parse($row->period_end)->startOfDay();
            $payoutDate = $periodEnd->copy()->addDay()->startOfMonth()->toDateString();

            DB::table('plan_period_returns')
                ->where('id', $row->id)
                ->update(['payout_date' => $payoutDate]);
        }

        Schema::table('plan_period_returns', function (Blueprint $table): void {
            $table->unique(['plan_id', 'payout_date'], 'plan_period_returns_plan_payout_date_unique');
        });
    }

    public function down(): void
    {
        Schema::table('plan_period_returns', function (Blueprint $table): void {
            $table->dropUnique('plan_period_returns_plan_payout_date_unique');
            $table->dropColumn('payout_date');
        });
    }
};
