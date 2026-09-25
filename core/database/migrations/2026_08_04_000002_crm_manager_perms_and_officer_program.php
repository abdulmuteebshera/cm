<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('crm_staff_permission')) {
            Schema::create('crm_staff_permission', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('crm_staff_id');
                $table->unsignedBigInteger('crm_permission_id');
                $table->unique(['crm_staff_id', 'crm_permission_id'], 'crm_staff_perm_unique');
            });
        }

        if (Schema::hasTable('crm_clients') && !Schema::hasColumn('crm_clients', 'last_activity_at')) {
            Schema::table('crm_clients', function (Blueprint $table): void {
                $table->timestamp('last_activity_at')->nullable()->after('next_follow_up');
                $table->decimal('upfront_commission', 28, 8)->default(0)->after('committed_aum');
                $table->decimal('retention_commission_ytd', 28, 8)->default(0)->after('upfront_commission');
                $table->date('funded_on')->nullable()->after('retention_commission_ytd');
            });
        }

        if (Schema::hasTable('crm_staff') && !Schema::hasColumn('crm_staff', 'lifetime_aum')) {
            Schema::table('crm_staff', function (Blueprint $table): void {
                $table->decimal('lifetime_aum', 28, 8)->default(0)->after('is_super');
                $table->string('rank_tier', 40)->nullable()->after('lifetime_aum');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_staff_permission');
    }
};
