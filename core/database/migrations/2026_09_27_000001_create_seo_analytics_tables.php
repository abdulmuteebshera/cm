<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('seo_visitor_sessions')) {
            Schema::create('seo_visitor_sessions', function (Blueprint $table): void {
                $table->id();
                $table->string('visitor_id', 64)->index();
                $table->string('ip', 45)->nullable()->index();
                $table->string('country', 100)->nullable()->index();
                $table->string('country_code', 8)->nullable();
                $table->string('region', 120)->nullable();
                $table->string('city', 120)->nullable()->index();
                $table->decimal('lat', 10, 7)->nullable();
                $table->decimal('lng', 10, 7)->nullable();
                $table->string('device_type', 20)->nullable()->index();
                $table->string('browser', 80)->nullable();
                $table->string('os', 80)->nullable();
                $table->string('user_agent', 500)->nullable();
                $table->string('channel', 40)->nullable()->index();
                $table->string('source', 120)->nullable()->index();
                $table->string('medium', 80)->nullable();
                $table->string('campaign', 160)->nullable();
                $table->string('utm_term', 160)->nullable();
                $table->string('utm_content', 160)->nullable();
                $table->string('referrer', 500)->nullable();
                $table->string('referrer_host', 190)->nullable();
                $table->string('landing_page', 190)->nullable()->index();
                $table->string('exit_page', 190)->nullable();
                $table->unsignedInteger('page_views')->default(0);
                $table->unsignedInteger('clicks')->default(0);
                $table->unsignedInteger('duration_seconds')->default(0);
                $table->unsignedInteger('engaged_seconds')->default(0);
                $table->timestamp('first_seen_at')->nullable()->index();
                $table->timestamp('last_seen_at')->nullable()->index();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('seo_page_views')) {
            Schema::create('seo_page_views', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('session_id')->index();
                $table->string('visitor_id', 64)->index();
                $table->string('url', 500);
                $table->string('path', 190)->index();
                $table->string('title', 190)->nullable();
                $table->string('referrer', 500)->nullable();
                $table->unsignedInteger('duration_seconds')->default(0);
                $table->unsignedTinyInteger('scroll_depth')->default(0);
                $table->timestamp('entered_at')->nullable()->index();
                $table->timestamp('exited_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('seo_events')) {
            Schema::create('seo_events', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('session_id')->index();
                $table->string('visitor_id', 64)->index();
                $table->unsignedBigInteger('page_view_id')->nullable()->index();
                $table->string('event_type', 40)->index();
                $table->string('label', 190)->nullable();
                $table->string('target_url', 500)->nullable();
                $table->string('path', 190)->nullable();
                $table->json('meta')->nullable();
                $table->timestamp('occurred_at')->nullable()->index();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_events');
        Schema::dropIfExists('seo_page_views');
        Schema::dropIfExists('seo_visitor_sessions');
    }
};
