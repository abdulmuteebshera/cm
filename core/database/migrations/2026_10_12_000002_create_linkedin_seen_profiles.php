<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('linkedin_seen_profiles')) {
            Schema::create('linkedin_seen_profiles', function (Blueprint $table): void {
                $table->id();
                $table->string('profile_url')->unique();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('linkedin_settings') && !Schema::hasColumn('linkedin_settings', 'search_cursor')) {
            Schema::table('linkedin_settings', function (Blueprint $table): void {
                $table->unsignedInteger('search_cursor')->default(0);
            });
        }

        if (Schema::hasTable('linkedin_prospects') && Schema::hasTable('linkedin_seen_profiles')) {
            $now = now();
            foreach (DB::table('linkedin_prospects')->pluck('profile_url') as $url) {
                DB::table('linkedin_seen_profiles')->insertOrIgnore([
                    'profile_url' => $url,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('linkedin_seen_profiles');
        if (Schema::hasTable('linkedin_settings') && Schema::hasColumn('linkedin_settings', 'search_cursor')) {
            Schema::table('linkedin_settings', function (Blueprint $table): void {
                $table->dropColumn('search_cursor');
            });
        }
    }
};
