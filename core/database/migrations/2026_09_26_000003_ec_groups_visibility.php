<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ec_groups') && !Schema::hasColumn('ec_groups', 'is_private')) {
            Schema::table('ec_groups', function (Blueprint $table): void {
                $table->tinyInteger('is_private')->default(0)->after('description')
                    ->comment('Admin only: 1 = hidden from campaign users');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('ec_groups', 'is_private')) {
            Schema::table('ec_groups', function (Blueprint $table): void {
                $table->dropColumn('is_private');
            });
        }
    }
};
