<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('announcements') && !Schema::hasColumn('announcements', 'show_on_dashboard')) {
            Schema::table('announcements', function (Blueprint $table): void {
                $table->tinyInteger('show_on_dashboard')->default(0)->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('announcements') && Schema::hasColumn('announcements', 'show_on_dashboard')) {
            Schema::table('announcements', function (Blueprint $table): void {
                $table->dropColumn('show_on_dashboard');
            });
        }
    }
};
