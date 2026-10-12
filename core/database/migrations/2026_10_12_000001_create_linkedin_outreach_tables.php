<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('linkedin_settings')) {
            Schema::create('linkedin_settings', function (Blueprint $table): void {
                $table->id();
                $table->boolean('enabled')->default(true);
                $table->boolean('require_approval')->default(true);
                $table->unsignedTinyInteger('daily_connection_limit')->default(20);
                $table->boolean('posting_enabled')->default(true);
                $table->unsignedTinyInteger('post_interval_days')->default(3);
                $table->string('client_id')->nullable();
                $table->text('client_secret')->nullable();
                $table->text('access_token')->nullable();
                $table->text('refresh_token')->nullable();
                $table->timestamp('token_expires_at')->nullable();
                $table->string('member_id')->nullable();
                $table->string('member_name')->nullable();
                $table->string('member_email')->nullable();
                $table->timestamp('connected_at')->nullable();
                $table->text('last_error')->nullable();
                $table->timestamp('last_daily_run_at')->nullable();
                $table->unsignedInteger('post_template_index')->default(0);
                $table->text('audience_titles')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('linkedin_prospects')) {
            Schema::create('linkedin_prospects', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('company');
                $table->string('title')->nullable();
                $table->string('profile_url');
                $table->string('location')->nullable();
                $table->text('public_note')->nullable();
                $table->string('fit', 32)->default('pending');
                $table->string('outreach_status', 32)->default('new');
                $table->string('follow_up_status', 32)->default('none');
                $table->string('response', 32)->default('none');
                $table->boolean('do_not_contact')->default(false);
                $table->string('stop_reason', 32)->nullable();
                $table->date('outreach_date')->nullable();
                $table->date('redraft_on')->nullable();
                $table->timestamp('researched_at')->nullable();
                $table->timestamp('response_at')->nullable();
                $table->timestamp('qualified_at')->nullable();
                $table->timestamps();

                $table->unique('profile_url');
                $table->index('outreach_status');
                $table->index('do_not_contact');
            });
        }

        if (!Schema::hasTable('linkedin_messages')) {
            Schema::create('linkedin_messages', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('prospect_id')->constrained('linkedin_prospects')->cascadeOnDelete();
                $table->string('kind', 32);
                $table->text('body');
                $table->string('basis')->nullable();
                $table->string('status', 32)->default('pending_approval');
                $table->timestamp('approved_at')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();

                $table->index(['kind', 'status']);
            });
        }

        if (!Schema::hasTable('linkedin_posts')) {
            Schema::create('linkedin_posts', function (Blueprint $table): void {
                $table->id();
                $table->text('commentary');
                $table->string('image')->nullable();
                $table->string('status', 32)->default('pending_approval');
                $table->string('source', 32)->default('manual');
                $table->date('scheduled_for')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->string('linkedin_post_id')->nullable();
                $table->text('error')->nullable();
                $table->timestamps();

                $table->index('status');
            });
        }

        if (!Schema::hasTable('linkedin_reports')) {
            Schema::create('linkedin_reports', function (Blueprint $table): void {
                $table->id();
                $table->date('report_date')->unique();
                $table->unsignedInteger('researched')->default(0);
                $table->unsignedInteger('approved')->default(0);
                $table->unsignedInteger('replies')->default(0);
                $table->unsignedInteger('qualified')->default(0);
                $table->unsignedInteger('declined')->default(0);
                $table->unsignedInteger('opted_out')->default(0);
                $table->unsignedInteger('posts_published')->default(0);
                $table->text('notes')->nullable();
                $table->json('summary')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('linkedin_reports');
        Schema::dropIfExists('linkedin_posts');
        Schema::dropIfExists('linkedin_messages');
        Schema::dropIfExists('linkedin_prospects');
        Schema::dropIfExists('linkedin_settings');
    }
};
