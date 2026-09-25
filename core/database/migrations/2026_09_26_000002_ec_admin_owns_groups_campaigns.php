<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ec_groups') && !Schema::hasColumn('ec_groups', 'ec_admin_id')) {
            Schema::table('ec_groups', function (Blueprint $table): void {
                $table->unsignedBigInteger('ec_admin_id')->nullable()->after('id');
                $table->index('ec_admin_id');
            });
        }

        if (Schema::hasTable('ec_campaigns') && !Schema::hasColumn('ec_campaigns', 'ec_admin_id')) {
            Schema::table('ec_campaigns', function (Blueprint $table): void {
                $table->unsignedBigInteger('ec_admin_id')->nullable()->after('id');
                $table->index('ec_admin_id');
            });
        }

        if (Schema::hasTable('ec_groups') && Schema::hasColumn('ec_groups', 'ec_user_id')) {
            DB::statement('ALTER TABLE ec_groups MODIFY ec_user_id BIGINT UNSIGNED NULL');
        }

        if (Schema::hasTable('ec_campaigns') && Schema::hasColumn('ec_campaigns', 'ec_user_id')) {
            DB::statement('ALTER TABLE ec_campaigns MODIFY ec_user_id BIGINT UNSIGNED NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('ec_groups', 'ec_admin_id')) {
            Schema::table('ec_groups', function (Blueprint $table): void {
                $table->dropIndex(['ec_admin_id']);
                $table->dropColumn('ec_admin_id');
            });
        }

        if (Schema::hasColumn('ec_campaigns', 'ec_admin_id')) {
            Schema::table('ec_campaigns', function (Blueprint $table): void {
                $table->dropIndex(['ec_admin_id']);
                $table->dropColumn('ec_admin_id');
            });
        }
    }
};
