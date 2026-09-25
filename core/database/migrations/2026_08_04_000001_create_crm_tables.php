<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('crm_roles')) {
            Schema::create('crm_roles', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('portal', 40)->default('crm')->comment('crm|manager|agent|trader|finance');
                $table->text('description')->nullable();
                $table->tinyInteger('is_system')->default(0)->comment('1 = cannot delete');
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('crm_permissions')) {
            Schema::create('crm_permissions', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('group', 80)->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('crm_role_permission')) {
            Schema::create('crm_role_permission', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('crm_role_id');
                $table->unsignedBigInteger('crm_permission_id');
                $table->unique(['crm_role_id', 'crm_permission_id'], 'crm_role_perm_unique');
            });
        }

        if (!Schema::hasTable('crm_staff')) {
            Schema::create('crm_staff', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('crm_role_id')->nullable();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('phone', 50)->nullable();
                $table->string('password');
                $table->string('employee_code', 40)->nullable();
                $table->string('department', 80)->nullable();
                $table->string('title', 120)->nullable();
                $table->string('image')->nullable();
                $table->tinyInteger('status')->default(1)->comment('1 active, 0 disabled');
                $table->tinyInteger('is_super')->default(0);
                $table->timestamp('last_login_at')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('crm_clients')) {
            Schema::create('crm_clients', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('owner_staff_id')->nullable()->comment('investment officer');
                $table->unsignedInteger('user_id')->nullable()->comment('linked portal investor if any');
                $table->string('name');
                $table->string('email')->nullable();
                $table->string('phone', 50)->nullable();
                $table->string('company', 160)->nullable();
                $table->string('country', 80)->nullable();
                $table->string('city', 80)->nullable();
                $table->string('source', 80)->nullable();
                $table->string('stage', 40)->default('lead')->comment('lead|qualified|onboarding|active|closed');
                $table->decimal('expected_aum', 28, 8)->default(0);
                $table->decimal('committed_aum', 28, 8)->default(0);
                $table->text('notes')->nullable();
                $table->date('next_follow_up')->nullable();
                $table->timestamps();
                $table->index(['owner_staff_id', 'stage']);
            });
        }

        if (!Schema::hasTable('crm_referrals')) {
            Schema::create('crm_referrals', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('staff_id');
                $table->unsignedBigInteger('client_id')->nullable();
                $table->string('referrer_name')->nullable();
                $table->string('referrer_email')->nullable();
                $table->string('prospect_name');
                $table->string('prospect_email')->nullable();
                $table->string('prospect_phone', 50)->nullable();
                $table->string('status', 40)->default('pending');
                $table->decimal('potential_amount', 28, 8)->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('crm_commissions')) {
            Schema::create('crm_commissions', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('staff_id');
                $table->unsignedBigInteger('client_id')->nullable();
                $table->string('title');
                $table->string('period', 40)->nullable();
                $table->decimal('base_amount', 28, 8)->default(0);
                $table->decimal('rate_percent', 8, 4)->default(0);
                $table->decimal('commission_amount', 28, 8)->default(0);
                $table->string('status', 40)->default('pending')->comment('pending|approved|paid|rejected');
                $table->text('notes')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('crm_pitch_decks')) {
            Schema::create('crm_pitch_decks', function (Blueprint $table): void {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('file_path');
                $table->string('original_name')->nullable();
                $table->string('audience', 40)->default('all')->comment('all|agent|manager|trader|finance');
                $table->unsignedBigInteger('uploaded_by')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('crm_fund_positions')) {
            Schema::create('crm_fund_positions', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('asset_class', 80)->nullable();
                $table->string('venue', 120)->nullable();
                $table->string('symbol', 80)->nullable();
                $table->string('currency', 10)->default('USD');
                $table->decimal('allocated_amount', 28, 8)->default(0);
                $table->decimal('market_value', 28, 8)->default(0);
                $table->decimal('pnl', 28, 8)->default(0);
                $table->decimal('pnl_percent', 10, 4)->default(0);
                $table->string('status', 40)->default('open');
                $table->date('opened_on')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('managed_by')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('crm_finance_entries')) {
            Schema::create('crm_finance_entries', function (Blueprint $table): void {
                $table->id();
                $table->date('entry_date');
                $table->string('type', 40)->comment('income|expense|asset|liability|equity');
                $table->string('category', 80)->nullable();
                $table->string('title');
                $table->decimal('amount', 28, 8)->default(0);
                $table->string('currency', 10)->default('USD');
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
                $table->index(['entry_date', 'type']);
            });
        }

        if (!Schema::hasTable('crm_activity_logs')) {
            Schema::create('crm_activity_logs', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('staff_id')->nullable();
                $table->string('action', 120);
                $table->string('subject_type', 80)->nullable();
                $table->unsignedBigInteger('subject_id')->nullable();
                $table->text('meta')->nullable();
                $table->string('ip', 45)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_activity_logs');
        Schema::dropIfExists('crm_finance_entries');
        Schema::dropIfExists('crm_fund_positions');
        Schema::dropIfExists('crm_pitch_decks');
        Schema::dropIfExists('crm_commissions');
        Schema::dropIfExists('crm_referrals');
        Schema::dropIfExists('crm_clients');
        Schema::dropIfExists('crm_staff');
        Schema::dropIfExists('crm_role_permission');
        Schema::dropIfExists('crm_permissions');
        Schema::dropIfExists('crm_roles');
    }
};
