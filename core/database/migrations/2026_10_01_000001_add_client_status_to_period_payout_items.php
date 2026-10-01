<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('period_payout_items', function (Blueprint $table): void {
            $table->string('status', 20)->default('pending')->after('rate_percent');
            $table->decimal('calculated_amount', 28, 8)->default(0)->after('amount');
            $table->boolean('amount_edited')->default(false)->after('calculated_amount');
            $table->unsignedBigInteger('approved_by')->nullable()->after('transaction_id');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });

        DB::table('period_payout_items')->whereNotNull('transaction_id')->update([
            'status'      => 'approved',
            'approved_at' => DB::raw('updated_at'),
        ]);

        DB::statement('UPDATE period_payout_items SET calculated_amount = amount WHERE calculated_amount = 0');
    }

    public function down(): void
    {
        Schema::table('period_payout_items', function (Blueprint $table): void {
            $table->dropColumn(['status', 'calculated_amount', 'amount_edited', 'approved_by', 'approved_at']);
        });
    }
};
