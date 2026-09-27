<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('seo_visitor_sessions')) {
            Schema::table('seo_visitor_sessions', function (Blueprint $table): void {
                if (!Schema::hasColumn('seo_visitor_sessions', 'channel')) {
                    $table->string('channel', 40)->nullable();
                }
                if (!Schema::hasColumn('seo_visitor_sessions', 'clicks')) {
                    $table->unsignedInteger('clicks')->default(0);
                }
                if (!Schema::hasColumn('seo_visitor_sessions', 'exit_path')) {
                    $table->string('exit_path', 190)->nullable();
                }
                if (!Schema::hasColumn('seo_visitor_sessions', 'engaged_seconds')) {
                    $table->unsignedInteger('engaged_seconds')->default(0);
                }
            });
        }

        if (Schema::hasTable('seo_events')) {
            Schema::table('seo_events', function (Blueprint $table): void {
                if (!Schema::hasColumn('seo_events', 'meta')) {
                    $table->json('meta')->nullable();
                }
                if (!Schema::hasColumn('seo_events', 'occurred_at')) {
                    $table->timestamp('occurred_at')->nullable()->index();
                }
            });
        }
    }

    public function down(): void
    {
        //
    }
};
