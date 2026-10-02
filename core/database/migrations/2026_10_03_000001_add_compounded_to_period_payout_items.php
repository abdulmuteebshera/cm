<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('period_payout_items', function (Blueprint $table): void {
            $table->boolean('compounded')->default(false)->after('amount_edited');
        });
    }

    public function down(): void
    {
        Schema::table('period_payout_items', function (Blueprint $table): void {
            $table->dropColumn('compounded');
        });
    }
};
