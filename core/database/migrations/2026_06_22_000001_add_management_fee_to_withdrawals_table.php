<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('withdrawals', 'management_fee')) {
            Schema::table('withdrawals', function (Blueprint $table): void {
                $table->decimal('management_fee', 28, 8)->default(0)->after('charge');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('withdrawals', 'management_fee')) {
            Schema::table('withdrawals', function (Blueprint $table): void {
                $table->dropColumn('management_fee');
            });
        }
    }
};
