<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ec_admins')) {
            Schema::create('ec_admins', function (Blueprint $table): void {
                $table->id();
                $table->string('username')->unique();
                $table->string('password');
                $table->string('name')->default('Administrator');
                $table->tinyInteger('status')->default(1);
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ec_users')) {
            Schema::create('ec_users', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('username')->unique();
                $table->string('email')->nullable();
                $table->string('password');
                $table->tinyInteger('status')->default(1);
                $table->timestamp('last_login_at')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ec_mail_settings')) {
            Schema::create('ec_mail_settings', function (Blueprint $table): void {
                $table->id();
                $table->string('from_email');
                $table->string('from_name')->default('Crownmaire Capital');
                $table->string('reply_to')->nullable();
                $table->string('smtp_host');
                $table->unsignedSmallInteger('smtp_port')->default(587);
                $table->string('smtp_encryption', 10)->default('tls');
                $table->string('smtp_username');
                $table->text('smtp_password');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ec_templates')) {
            Schema::create('ec_templates', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('subject');
                $table->longText('body_html');
                $table->longText('body_text')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ec_groups')) {
            Schema::create('ec_groups', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('ec_user_id');
                $table->string('name');
                $table->text('description')->nullable();
                $table->timestamps();
                $table->index('ec_user_id');
            });
        }

        if (!Schema::hasTable('ec_group_members')) {
            Schema::create('ec_group_members', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('ec_group_id');
                $table->string('email');
                $table->string('name')->nullable();
                $table->json('merge_data')->nullable();
                $table->timestamps();
                $table->index(['ec_group_id', 'email']);
            });
        }

        if (!Schema::hasTable('ec_campaigns')) {
            Schema::create('ec_campaigns', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('ec_user_id');
                $table->unsignedBigInteger('ec_template_id');
                $table->unsignedBigInteger('ec_group_id')->nullable();
                $table->string('name');
                $table->string('subject');
                $table->longText('body_html');
                $table->longText('body_text')->nullable();
                $table->string('status', 20)->default('draft');
                $table->unsignedSmallInteger('send_delay_seconds')->default(10);
                $table->unsignedInteger('sent_count')->default(0);
                $table->unsignedInteger('failed_count')->default(0);
                $table->unsignedInteger('total_recipients')->default(0);
                $table->timestamp('next_send_at')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('paused_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                $table->index(['status', 'next_send_at']);
                $table->index('ec_user_id');
            });
        }

        if (!Schema::hasTable('ec_campaign_recipients')) {
            Schema::create('ec_campaign_recipients', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('ec_campaign_id');
                $table->string('email');
                $table->string('name')->nullable();
                $table->json('merge_data')->nullable();
                $table->string('status', 20)->default('pending');
                $table->timestamp('sent_at')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamps();
                $table->index(['ec_campaign_id', 'status']);
            });
        }

        if (!Schema::hasTable('ec_activity_logs')) {
            Schema::create('ec_activity_logs', function (Blueprint $table): void {
                $table->id();
                $table->string('actor_type', 20);
                $table->unsignedBigInteger('actor_id')->nullable();
                $table->string('action', 80);
                $table->string('subject_type', 40)->nullable();
                $table->unsignedBigInteger('subject_id')->nullable();
                $table->text('description')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();
                $table->index(['actor_type', 'actor_id']);
                $table->index('created_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ec_activity_logs');
        Schema::dropIfExists('ec_campaign_recipients');
        Schema::dropIfExists('ec_campaigns');
        Schema::dropIfExists('ec_group_members');
        Schema::dropIfExists('ec_groups');
        Schema::dropIfExists('ec_templates');
        Schema::dropIfExists('ec_mail_settings');
        Schema::dropIfExists('ec_users');
        Schema::dropIfExists('ec_admins');
    }
};
